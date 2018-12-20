<?php

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Mink\Element\NodeElement;
use Matriphe\ISO639\ISO639;
use PHPUnit\Framework\Assert;

class LocalizationContext extends BaseContext
{
    /**
     * @throws \Exception
     *
     * @Then the page hreflang markup should be valid
     */
    public function thePageHreflangMarkupShouldBeValid()
    {
        $currentUrl = $this->getCurrentUrl();

        $hreflangMetaTags = $this->getSession()->getPage()->findAll(
            'xpath',
            '//head/link[@rel="alternate" and @hreflang]'
        );

        Assert::assertNotEmpty(
            $hreflangMetaTags,
            sprintf('No hreflang meta tags have been found in %s', $currentUrl)
        );

        $localeIsoValidator = new ISO639();
        $selfReferenceFound = false;

        /** @var NodeElement $hreflangMetaTag */
        foreach ($hreflangMetaTags as $hreflangMetaTag) {
            $alternateLocale = $hreflangMetaTag->getAttribute('hreflang');

            if ('x-default' === $alternateLocale) {
                continue;
            }

            Assert::assertNotEmpty(
                $localeIsoValidator->languageByCode1($alternateLocale),
                sprintf(
                    'Wrong locale ISO-639-1 code "%s" in hreflang meta tag in url %s: %s',
                    $alternateLocale,
                    $currentUrl,
                    $hreflangMetaTag->getOuterHtml()
                )
            );

            $alternateLink = $hreflangMetaTag->getAttribute('href');

            if ($alternateLink === $currentUrl) {
                $selfReferenceFound = true;
            } else {
                $this->getSession()->visit($alternateLink);

                $reciprocalHreflangMetaTag = $this->getSession()->getPage()->find(
                    'xpath',
                    sprintf('//head/link[@rel="alternate" and @hreflang and @href="%s"]', $currentUrl)
                );

                Assert::assertNotNull(
                    $reciprocalHreflangMetaTag,
                    sprintf(
                        'No reciprocal hreflang meta tag has been found in %s pointing to %s',
                        $alternateLink,
                        $currentUrl
                    )
                );

                $this->getSession()->back();
            }
        }

        Assert::assertTrue(
            $selfReferenceFound,
            sprintf('No self-referencing hreflang meta tag has been found in %s', $currentUrl)
        );
    }

    /**
     * @throws \Exception
     *
     * @Then the page hreflang markup should not be valid
     */
    public function thePageHreflangMarkupShouldNotBeValid()
    {
        $this->assertInverse(
            [$this, 'thePageHreflangMarkupShouldBeValid'],
            'HTML markup should not be valid.'
        );
    }
}
