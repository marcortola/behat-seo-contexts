<?php

namespace MOrtola\BehatSEOContexts;

use PHPUnit\Framework\Assert;

class MetaContext extends BaseContext
{
    /**
     * @param $expectedCanonicalUrl
     *
     * @throws \Exception
     *
     * @Then the page canonical should be :expectedCanonicalUrl
     */
    public function thePageCanonicalShouldBe($expectedCanonicalUrl)
    {
        $expectedCanonicalUrl = $this->toAbsoluteUrl($expectedCanonicalUrl);

        $canonicalElement = $this->getSession()->getPage()->find(
            'xpath',
            '//head/link[@rel="canonical"]'
        );

        Assert::assertNotNull(
            $canonicalElement,
            'Canonical element does not exist'
        );

        $canonicalUrl = $canonicalElement->getAttribute('href');

        Assert::assertEquals(
            $expectedCanonicalUrl,
            $canonicalUrl,
            sprintf('Canonical url should be "%s". Got "%s"', $expectedCanonicalUrl, $canonicalUrl)
        );
    }

    /**
     * @param $expectedMetaTitle
     *
     * @throws \Exception
     *
     * @Then the page meta title should be :expectedMetaTitle
     */
    public function thePageMetaTitleShouldBe($expectedMetaTitle)
    {
        $metaTitleElement = $this->getSession()->getPage()->find('css', 'title');

        Assert::assertNotNull(
            $metaTitleElement,
            'Meta title does not exist'
        );

        $metaTitle = $metaTitleElement->getText();

        Assert::assertEquals(
            $expectedMetaTitle,
            $metaTitle,
            sprintf(
                'Meta title should be "%s". Got "%s"',
                $expectedMetaTitle,
                $metaTitle
            )
        );
    }

    /**
     * @param $expectedMetaDescription
     *
     * @throws \Exception
     *
     * @Then the page meta description should be :expectedMetaDescription
     */
    public function thePageMetaDescriptionShouldBe($expectedMetaDescription)
    {
        $metaDescriptionElement = $this->getSession()->getPage()->find(
            'xpath',
            '//head/meta[@name="description"]'
        );

        Assert::assertNotNull(
            $metaDescriptionElement,
            'Meta description does not exist'
        );

        $metaDescription = $metaDescriptionElement->getAttribute('content');

        Assert::assertEquals(
            $expectedMetaDescription,
            $metaDescription,
            sprintf(
                'Meta description should be "%s". Got "%s"',
                $expectedMetaDescription,
                $metaDescription
            )
        );
    }
}
