<?php

namespace MOrtola\BehatSEOContexts;

use PHPUnit\Framework\Assert;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class SitemapContext extends BaseContext
{
    const SITEMAP_SCHEMA_FILE = __DIR__.'/../resources/fixtures/schemas/sitemap.xsd';
    const SITEMAP_XHTML_SCHEMA_FILE = __DIR__.'/../resources/fixtures/schemas/sitemap_xhtml.xsd';
    const SITEMAP_INDEX_SCHEMA_FILE = __DIR__.'/../resources/fixtures/schemas/sitemap_index.xsd';

    /**
     * @var \DOMDocument
     */
    private $sitemapXml;

    /**
     * @param $sitemapType
     * @param $sitemapUrl
     *
     * @throws \Exception
     *
     * @Given /^the (index|multilanguage|) sitemap "(.*)"$/
     */
    public function theSitemap($sitemapType, $sitemapUrl)
    {
        $this->sitemapXml = $this->getSitemapXml($sitemapUrl);

        switch ($sitemapType) {
            case 'index':
                $sitemapSchemaFile = self::SITEMAP_INDEX_SCHEMA_FILE;

                break;
            case 'multilanguage':
                $sitemapSchemaFile = self::SITEMAP_XHTML_SCHEMA_FILE;

                break;
            default:
                $sitemapSchemaFile = self::SITEMAP_SCHEMA_FILE;

                break;
        }

        $this->assertValidSitemap($sitemapSchemaFile);
    }

    /**
     * @param string $sitemapUrl
     *
     * @return \DOMDocument
     * @throws \Exception
     */
    private function getSitemapXml($sitemapUrl)
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
     * @param string $sitemapSchemaFile
     *
     * @throws \Exception
     */
    private function assertValidSitemap($sitemapSchemaFile)
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
     * @param string $childSitemapUrl
     *
     * @throws \Exception
     *
     * @Then the index sitemap should have child :childSitemapUrl
     */
    public function theIndexSitemapShouldHaveChild($childSitemapUrl)
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

    /**
     * @return \DOMXPath
     */
    private function getXpathInspector()
    {
        $xpath = new \DOMXPath($this->sitemapXml);
        $xpath->registerNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xpath->registerNamespace('xhtml', 'http://www.w3.org/1999/xhtml');

        return $xpath;
    }

    /**
     * @param int $expectedChildrenCount
     *
     * @throws \Exception
     *
     * @Then /^the sitemap has ([0-9]+) children$/
     */
    public function theSitemapHasChildren($expectedChildrenCount)
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
     * @Then the multilanguage sitemap pass Google validation
     */
    public function theMultilanguageSitemapPassGoogleValidation()
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
                            'Url %s has not reciprocous Url for alternative link %s in Sitemap %s',
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
     *
     * @Then the sitemap URLs are alive
     */
    public function theSitemapUrlsAreAlive()
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
}
