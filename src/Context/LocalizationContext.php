<?php declare(strict_types=1);

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Mink\Element\NodeElement;
use Exception;
use Matriphe\ISO639\ISO639;
use PHPUnit\Framework\Assert;

class LocalizationContext extends BaseContext
{
    /**
     * @throws Exception
     *
     * @Then the page hreflang markup should be valid
     */
    public function thePageHreflangMarkupShouldBeValid(): void
    {
        $this->assertHreflangExists();
        $this->assertHreflangValidSelfReference();
        $this->assertHreflangValidIsoCodes();
        $this->assertHreflangCoherentXDefault();
        $this->assertHreflangValidReciprocal();
    }

    private function assertHreflangExists(): void
    {
        Assert::assertNotEmpty(
            $this->getHreflangElements(),
            sprintf('No hreflang meta tags have been found in %s', $this->getCurrentUrl())
        );
    }

    /**
     * @return NodeElement[]
     */
    private function getHreflangElements(): array
    {
        return $this->getSession()->getPage()->findAll(
            'xpath',
            '//head/link[@rel="alternate" and @hreflang]'
        );
    }

    private function assertHreflangValidSelfReference(): void
    {
        $selfReferenceFound = false;

        foreach ($this->getHreflangElements() as $hreflangMetaTag) {
            $alternateLink = $hreflangMetaTag->getAttribute('href');
            if ($alternateLink === $this->getCurrentUrl()) {
                $selfReferenceFound = true;
            }
        }

        Assert::assertTrue(
            $selfReferenceFound,
            sprintf('No self-referencing hreflang meta tag has been found in %s', $this->getCurrentUrl())
        );
    }

    private function assertHreflangValidIsoCodes(): void
    {
        $localeIsoValidator = new ISO639();
        foreach ($this->getHreflangElements() as $hreflangMetaTag) {
            $alternateLocale = $hreflangMetaTag->getAttribute('hreflang');

            if ('x-default' === $alternateLocale) {
                continue;
            }

            Assert::assertNotEmpty(
                $alternateLocale,
                'hreflang locale should not be empty.'
            );

            Assert::assertNotEmpty(
                $localeIsoValidator->languageByCode1($alternateLocale),
                sprintf(
                    'Wrong locale ISO-639-1 code "%s" in hreflang meta tag in url %s: %s',
                    $alternateLocale,
                    $this->getCurrentUrl(),
                    $this->getOuterHtml($hreflangMetaTag)
                )
            );
        }
    }

    private function assertHreflangCoherentXDefault(): void
    {
        foreach ($this->getHreflangElements() as $hreflangMetaTag) {
            if ('x-default' === $hreflangMetaTag->getAttribute('hreflang')) {
                $xDefault = $hreflangMetaTag->getAttribute('href');
            }
        }

        if (!isset($xDefault)) {
            return;
        }

        foreach ($this->getHreflangElements() as $hreflangMetaTag) {
            if ('x-default' !== $hreflangMetaTag->getAttribute('hreflang')) {
                $href = $hreflangMetaTag->getAttribute('href');

                Assert::assertNotNull($href);

                if (null === $href) {
                    continue;
                }

                $this->getSession()->visit($href);

                $hreflangAltDefault = $this->getSession()->getPage()->find(
                    'xpath',
                    '//head/link[@rel="alternate" and @hreflang="x-default"]'
                );

                Assert::assertNotNull($hreflangAltDefault);

                if (null === $hreflangAltDefault) {
                    continue;
                }

                Assert::assertEquals(
                    $xDefault,
                    $hreflangAltDefault->getAttribute('href')
                );

                $this->getSession()->back();
            }
        }
    }

    private function assertHreflangValidReciprocal(): void
    {
        $currentPageHreflangLinks = [];
        foreach ($this->getHreflangElements() as $hreflangElement) {
            $currentPageHreflangLinks[$hreflangElement->getAttribute('hreflang')] = $hreflangElement->getAttribute(
                'href'
            );
        }

        foreach ($currentPageHreflangLinks as $currentPageHreflangLink) {
            if ($currentPageHreflangLink === $this->getCurrentUrl()) {
                continue;
            }

            if ($currentPageHreflangLink) {
                $this->getSession()->visit($currentPageHreflangLink);
            }

            $referencedPageHreflangLinks = [];

            foreach ($this->getHreflangElements() as $hreflangElement) {
                $referencedPageHreflangLinks[$hreflangElement->getAttribute(
                    'hreflang'
                )] = $hreflangElement->getAttribute('href');
            }

            $this->getSession()->back();

            Assert::assertEquals(
                $currentPageHreflangLinks,
                $referencedPageHreflangLinks,
                'Missing or not coherent hreflang reciprocal links.'
            );
        }
    }

    /**
     * @throws Exception
     *
     * @Then the page hreflang markup should not be valid
     */
    public function thePageHreflangMarkupShouldNotBeValid(): void
    {
        $this->assertInverse(
            [$this, 'thePageHreflangMarkupShouldBeValid'],
            'hreflang markup should not be valid.'
        );
    }
}
