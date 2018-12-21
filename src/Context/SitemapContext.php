<?php

namespace MOrtola\BehatSEOContexts\Context;

use PHPUnit\Framework\Assert;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class SitemapContext extends BaseContext
{
    const SITEMAP_SCHEMA_FILE = __DIR__ . '/../Resources/schemas/sitemap.xsd';
    const SITEMAP_XHTML_SCHEMA_FILE = __DIR__ . '/../Resources/schemas/sitemap_xhtml.xsd';
    const SITEMAP_INDEX_SCHEMA_FILE = __DIR__ . '/../Resources/schemas/sitemap_index.xsd';

    /**
     * @var \DOMDocument
     */
    private $sitemapXml;

    /**
     * @throws \Exception
     *
     * @Given the sitemap :sitemapUrl
     */
    public function theSitemap(string $sitemapUrl)
    {
        $this->sitemapXml = $this->getSitemapXml($sitemapUrl);
    }

    /**
     * @throws \Exception
     */
    private function getSitemapXml(string $sitemapUrl): \DOMDocument
    {
        $sitemapUrl = $this->toAbsoluteUrl($sitemapUrl);

        $xml = new \DOMDocument();
        @$xmlLoaded = $xml->load($sitemapUrl);

        Assert::assertNotFalse(
            $xmlLoaded,
            sprintf(
                'Error loading %s Sitemap using DOMDocument',
                $sitemapUrl
            )
        );

        return $xml;
    }

    /**
     * @throws \Exception
     *
     * @Then the index sitemap should have a child with URL :childSitemapUrl
     */
    public function theIndexSitemapShouldHaveAChildWithUrl(string $childSitemapUrl)
    {
        $this->assertSitemapHasBeenRead();

        $xpathExpression = sprintf(
            '//sm:sitemapindex/sm:sitemap/sm:loc[contains(text(),"%s")]',
            $childSitemapUrl
        );

        Assert::assertGreaterThanOrEqual(
            1,
            $this->getXpathInspector()->query($xpathExpression)->length,
            sprintf(
                'Sitemap index %s has not child sitemap %s',
                $this->sitemapXml->documentURI,
                $childSitemapUrl
            )
        );
    }

    /**
     * @throws \Exception
     */
    private function assertSitemapHasBeenRead()
    {
        if (null == $this->sitemapXml) {
            throw new \Exception(
                'You should execute "Given the sitemap :sitemapUrl" step before executing this step.'
            );
        }
    }

    private function getXpathInspector(): \DOMXPath
    {
        $xpath = new \DOMXPath($this->sitemapXml);
        $xpath->registerNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xpath->registerNamespace('xhtml', 'http://www.w3.org/1999/xhtml');

        return $xpath;
    }

    /**
     * @throws \Exception
     *
     * @Then /^the sitemap should have ([0-9]+) children$/
     */
    public function theSitemapShouldHaveChildren(int $expectedChildrenCount)
    {
        $this->assertSitemapHasBeenRead();

        $sitemapChildrenCount = $this->getXpathInspector()
            ->query('/*[self::sm:sitemapindex or self::sm:urlset]/*[self::sm:sitemap or self::sm:url]/sm:loc')
            ->length;

        Assert::assertEquals(
            $expectedChildrenCount,
            $sitemapChildrenCount,
            sprintf(
                'Sitemap %s has %d children, expected value was: %d',
                $this->sitemapXml->documentURI,
                $sitemapChildrenCount,
                $expectedChildrenCount
            )
        );
    }

    /**
     * @throws \Exception
     *
     * @Then the multilanguage sitemap should pass Google validation
     */
    public function theMultilanguageSitemapShouldPassGoogleValidation()
    {
        $this->assertSitemapHasBeenRead();

        $this->assertValidSitemap(self::SITEMAP_XHTML_SCHEMA_FILE);

        $urlsNodes = $this->getXpathInspector()->query('//sm:urlset/sm:url');

        /** @var \DOMElement $urlNode */
        foreach ($urlsNodes as $urlNode) {
            $urlLoc = $urlNode->getElementsByTagName('loc')->item(0)->nodeValue;

            /** @var \DOMElement $alternateLink */
            foreach ($urlNode->getElementsByTagName('link') as $alternateLink) {
                $alternateLinkHref = $alternateLink->getAttribute('href');

                if ($alternateLinkHref !== $urlLoc) {
                    $alternateLinkNodes = $this->getXpathInspector()->query(
                        sprintf('//sm:urlset/sm:url/sm:loc[text()="%s"]', $alternateLinkHref)
                    );

                    Assert::assertGreaterThanOrEqual(
                        1,
                        $alternateLinkNodes->length,
                        sprintf(
                            'Url %s has not reciprocous URL for alternative link %s in Sitemap %s',
                            $urlLoc,
                            $alternateLinkHref,
                            $this->sitemapXml->documentURI
                        )
                    );
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function assertValidSitemap(string $sitemapSchemaFile)
    {
        Assert::assertFileExists(
            $sitemapSchemaFile,
            sprintf('Sitemap schema file %s does not exist', $sitemapSchemaFile)
        );

        Assert::assertTrue(
            @$this->sitemapXml->schemaValidate($sitemapSchemaFile),
            sprintf(
                'Sitemap %s does not pass validation using %s schema',
                $this->sitemapXml->documentURI,
                $sitemapSchemaFile
            )
        );
    }

    /**
     * @throws \Exception
     *
     * @Then the sitemap URLs should be alive
     */
    public function theSitemapUrlsShouldBeAlive()
    {
        $this->assertSitemapHasBeenRead();

        $locNodes = $this->getXpathInspector()->query('//sm:urlset/sm:url/sm:loc');

        /** @var \DOMElement $locNode */
        foreach ($locNodes as $locNode) {
            try {
                $this->visit($locNode->nodeValue);
            } catch (RouteNotFoundException $e) {
                throw new \Exception(
                    sprintf(
                        'Sitemap Url %s is not valid in Sitemap: %s. Exception: %s',
                        $locNode->nodeValue,
                        $this->sitemapXml->documentURI,
                        $e->getMessage()
                    )
                );
            }

            Assert::assertEquals(
                200,
                $this->getStatusCode(),
                sprintf(
                    'Sitemap Url %s is not valid in Sitemap: %s. Response status code: %s',
                    $locNode->nodeValue,
                    $this->sitemapXml->documentURI,
                    $this->getStatusCode()
                )
            );
        }
    }

    /**
     * @Then /^the (index |multilanguage |)sitemap should not be valid$/
     */
    public function theSitemapShouldNotBeValid(string $sitemapType = '')
    {
        $this->assertInverse(
            function () use ($sitemapType) {
                $this->theSitemapShouldBeValid($sitemapType);
            },
            sprintf('The sitemap is a valid %s sitemap.', $sitemapType)
        );
    }

    /**
     * @throws \Exception
     *
     * @Then /^the (index |multilanguage |)sitemap should be valid$/
     */
    public function theSitemapShouldBeValid(string $sitemapType = '')
    {
        $sitemapType = trim($sitemapType);
        $this->assertSitemapHasBeenRead();

        switch ($sitemapType) {
            case 'index':
                $sitemapSchemaFile = self::SITEMAP_INDEX_SCHEMA_FILE;

                break;
            case 'multilanguage':
                $sitemapSchemaFile = self::SITEMAP_XHTML_SCHEMA_FILE;

                break;
            default:
                $sitemapSchemaFile = self::SITEMAP_SCHEMA_FILE;
        }

        $this->assertValidSitemap($sitemapSchemaFile);
    }

    /**
     * @Then the multilanguage sitemap should not pass Google validation
     */
    public function theMultilanguageSitemapShouldNotPassGoogleValidation()
    {
        $this->assertInverse(
            [$this, 'theMultilanguageSitemapShouldPassGoogleValidation'],
            sprintf('The multilanguage sitemap passes Google validation.')
        );
    }
}
