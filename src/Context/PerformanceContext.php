<?php

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Symfony2Extension\Driver\KernelDriver;
use PHPUnit\Framework\Assert;

class PerformanceContext extends BaseContext
{
    const RES_EXT = [
        'PNG' => 'png',
        'HTML' => 'html',
        'JPEG' => 'jpeg',
        'GIF' => 'gif',
        'ICO' => 'ico',
        'JAVASCRIPT' => 'js',
        'CSS' => 'css',
        'CSS_INLINE_HEAD' => 'css-inline-head',
        'CSS_LINK_HEAD' => 'css-link-head',
    ];

    /**
     * @throws \Exception
     *
     * @Then /^Javascript code should load (async|defer)$/
     */
    public function javascriptFilesShouldLoadAsync()
    {
        foreach ($this->getPageResources(self::RES_EXT['JAVASCRIPT']) as $scriptElement) {
            Assert::assertTrue(
                $scriptElement->hasAttribute('async') || $scriptElement->hasAttribute('defer'),
                sprintf(
                    'Javascript file %s is render blocking in %s',
                    $this->getResourceUrl($scriptElement, self::RES_EXT['JAVASCRIPT']),
                    $this->getCurrentUrl()
                )
            );
        }
    }

    /**
     * @param string      $resourceType
     * @param bool        $selfHosted
     *
     * @param string|null $host
     *
     * @return NodeElement[]
     */
    private function getPageResources(string $resourceType, bool $selfHosted = true, string $host = null): array
    {
        if (!$xpath = $this->getResourceXpath($resourceType)) {
            return [];
        }

        if ($selfHosted) {
            $xpath = preg_replace(
                '/\[contains\(@(.*),/',
                sprintf('[(starts-with(@$1,"%s") or starts-with(@$1,"/")) and contains(@$1,', $this->webUrl),
                $xpath
            );
        } elseif ($host === 'external') {
            $xpath = preg_replace(
                '/\[contains\(@(.*),/',
                '[not(starts-with(@$1,"' . $this->webUrl . '") or starts-with(@$1,"/")) and contains(@$1,',
                $xpath
            );
        } elseif (null !== $host) {
            $xpath = preg_replace(
                '/\[contains\(@(.*),/',
                '[(starts-with(@$1,"' . $host . '") or starts-with(@$1,"/")) and contains(@$1,',
                $xpath
            );
        }

        return $this->getSession()->getPage()->findAll('xpath', $xpath);
    }

    private function getResourceXpath(string $resourceType): string
    {
        if (in_array($resourceType, [self::RES_EXT['JPEG'], self::RES_EXT['PNG'], self::RES_EXT['GIF']], true)) {
            return sprintf('//img[contains(@src,".%s")]', $resourceType);
        }
        if (in_array($resourceType, [self::RES_EXT['ICO'], self::RES_EXT['CSS']], true)) {
            return sprintf('//link[contains(@href,".%s")]', $resourceType);
        }
        if (self::RES_EXT['JAVASCRIPT'] === $resourceType) {
            return '//script[contains(@src,".js")]';
        }
        if (self::RES_EXT['CSS_INLINE_HEAD'] === $resourceType) {
            return '//head//style';
        }
        if (self::RES_EXT['CSS_LINK_HEAD'] === $resourceType) {
            return '//head//link[contains(@href,".css")]';
        }

        return '';
    }

    /**
     * @param NodeElement $element
     * @param string      $resourceType
     *
     * @return string
     * @throws \Exception
     */
    private function getResourceUrl(NodeElement $element, string $resourceType): string
    {
        $this->assertResourceTypeIsValid($resourceType);

        if (in_array($resourceType, [
            self::RES_EXT['PNG'],
            self::RES_EXT['JPEG'],
            self::RES_EXT['GIF'],
            self::RES_EXT['JAVASCRIPT']
        ], true)) {
            return $element->getAttribute('src');
        }

        if (in_array($resourceType, [self::RES_EXT['CSS'], self::RES_EXT['ICO']], true)) {
            return $element->getAttribute('href');
        }

        throw new \Exception(
            sprintf('%s resource type url is not implemented', $resourceType)
        );
    }

    private function assertResourceTypeIsValid(string $resourceType)
    {
        if (!in_array($resourceType, self::RES_EXT, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s resource type is not valid. Allowed types are: %s',
                    $resourceType,
                    implode(',', self::RES_EXT)
                )
            );
        }
    }

    /**
     * @throws \Exception
     *
     * @Then HTML code should be minified
     */
    public function htmlShouldBeMinified()
    {
        $this->assertContentIsMinified(
            $this->getSession()->getPage()->getContent(),
            $this->minimizeHtml($this->getSession()->getPage()->getContent())
        );
    }

    private function assertContentIsMinified(string $content, string $contentMinified)
    {
        Assert::assertSame(
            $content,
            $contentMinified,
            'Code is not minified.'
        );
    }

    private function minimizeCss(string $css): string
    {
        return preg_replace(
            [
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|
                ]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|
                "(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
            ],
            ['$1', '$1$2$3$4$5$6$7'],
            $css
        );
    }

    private function minimizeJs(string $js): string
    {
        return preg_replace(
            [
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|
                \s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|
                [gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
            ],
            ['$1', '$1$2'],
            $js
        );
    }

    private function minimizeHtml(string $html): string
    {
        return preg_replace(
            '/(?<=>)\s+|\s+(?=<)/',
            '',
            $html
        );
    }

    /**
     * @throws \Exception
     *
     * @Then CSS code should load deferred
     */
    public function cssFilesShouldLoadDeferred()
    {
        Assert::assertEmpty(
            $this->getPageResources(self::RES_EXT['CSS_LINK_HEAD'], true),
            sprintf(
                'Some self hosted css files are loading in head in %s',
                $this->getCurrentUrl()
            )
        );
    }

    /**
     * @throws \Exception
     *
     * @Then critical CSS code should exist in head
     */
    public function criticalCssShouldExistInHead()
    {
        Assert::assertNotEmpty(
            $this->getPageResources(self::RES_EXT['CSS_INLINE_HEAD']),
            sprintf(
                'No inline css is loading in head in %s',
                $this->getCurrentUrl()
            )
        );
    }

    /**
     * @Then HTML code should not be minified
     */
    public function htmlShouldNotBeMinified()
    {
        $this->assertInverse(
            [$this, 'htmlShouldBeMinified'],
            'HTML should not be minified.'
        );
    }

    /**
     * @Then /^(CSS|Javascript) code should not be minified$/
     * @param string $resourceType
     */
    public function cssOrJavascriptFilesShouldNotBeMinified(string $resourceType)
    {
        $this->assertInverse(
            function () use ($resourceType) {
                $this->cssOrJavascriptFilesShouldBeMinified($resourceType);
            },
            sprintf('%s should not be minified.', $resourceType)
        );
    }

    /**
     * @param string $resourceType
     *
     * @throws UnsupportedDriverActionException
     * @throws \Exception
     * @Then /^(CSS|Javascript) code should be minified$/
     */
    public function cssOrJavascriptFilesShouldBeMinified(string $resourceType)
    {
        $this->doesNotSupportDriver(KernelDriver::class);

        $resourceType = 'Javascript' === $resourceType ? 'js' : 'css';

        foreach ($this->getPageResources($resourceType) as $element) {
            $this->getSession()->visit($this->getResourceUrl($element, $resourceType));

            $this->assertContentIsMinified(
                $this->getSession()->getPage()->getContent(),
                'js' === $resourceType ?
                    $this->minimizeJs($this->getSession()->getPage()->getContent())
                    : $this->minimizeCss($this->getSession()->getPage()->getContent())
            );

            $this->getSession()->back();
        }
    }

    /**
     * @Then critical CSS code should not exist in head
     */
    public function criticalCssShouldNotExistInHead()
    {
        $this->assertInverse(
            [$this, 'criticalCssShouldExistInHead'],
            'Critical CSS exist in head.'
        );
    }

    /**
     * @Then /^browser cache should not be enabled for (.+\..+|external|internal) (png|jpeg|gif|ico|js|css) resources$/
     * @param string $host
     * @param string $resourceType
     */
    public function browserCacheMustNotBeEnabledForResources(string $host, string $resourceType)
    {
        $this->assertInverse(
            function () use ($host, $resourceType) {
                $this->browserCacheMustBeEnabledForResources($host, $resourceType);
            },
            sprintf('Browser cache is enabled for %s resources.', $resourceType)
        );
    }

    /**
     * @param string $host
     * @param string $resourceType
     *
     * @throws UnsupportedDriverActionException
     * @throws \Exception
     * @Then /^browser cache should be enabled for (.+\..+|external|internal) (png|jpeg|gif|ico|js|css) resources$/
     */
    public function browserCacheMustBeEnabledForResources(string $host, string $resourceType)
    {
        $this->doesNotSupportDriver(KernelDriver::class);
        switch ($host) {
        case 'internal':
            $elements = $this->getPageResources($resourceType, true);
                break;
        case 'external':
            $elements = $this->getPageResources($resourceType, false, $host);
                break;
        default:
            $elements = $this->getPageResources($resourceType, false, $host);
                break;
        }
        $this->checkResourceCache($elements[array_rand($elements)], $resourceType);
        $this->getSession()->back();
    }

    /**
     * @Then /^Javascript code should not load (async|defer)$/
     */
    public function jsShouldNotLoadAsyncOr()
    {
        $this->assertInverse(
            [$this, 'javascriptFilesShouldLoadAsync'],
            'All JS files load async.'
        );
    }

    /**
     * @param        $element
     * @param string $resourceType
     *
     * @throws \Exception
     */
    private function checkResourceCache($element, $resourceType)
    {
        $this->getSession()->visit($this->getResourceUrl($element, $resourceType));
        $headers = array_change_key_case($this->getSession()->getResponseHeaders());

        Assert::assertTrue(
            array_key_exists('cache-control', $headers),
            sprintf(
                'Browser cache is not enabled for %s resources. Cache-Control HTTP header was not received.',
                $resourceType
            )
        );

        Assert::assertNotContains(
            '-no',
            $headers['cache-control'],
            sprintf(
                'Browser cache is not enabled for %s resources. Cache-Control HTTP header is "no-cache".',
                $resourceType
            )
        );
    }
}
