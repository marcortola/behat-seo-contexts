<?php

namespace MOrtola\BehatWebsiteContexts;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Element\NodeElement;
use HtmlValidator\Exception\ServerException;
use HtmlValidator\Message;
use HtmlValidator\Response;
use HtmlValidator\Validator;
use Matriphe\ISO639\ISO639;
use PHPUnit\Framework\Assert;

class SEOContext extends BaseContext
{
    /**
     * @var RobotsContext
     */
    private $robotsContext;

    /**
     * @param BeforeScenarioScope $scope
     *
     * @BeforeScenario
     */
    public function initOtherContexts(BeforeScenarioScope $scope)
    {
        $this->robotsContext = $scope->getEnvironment()->getContext('MOrtola\BehatWebsiteContexts\RobotsContext');
    }

    /**
     * @throws \Exception
     *
     * @Then the page should not be noindex
     */
    public function thePageShouldNotBeNoindex()
    {
        $metaRobotsElement = $this->getSession()->getPage()->find(
            'xpath',
            '//head/meta[@name="robots"]'
        );

        if (null != $metaRobotsElement) {
            $metaRobots = $metaRobotsElement->getAttribute('content');

            Assert::assertNotContains(
                'noindex',
                strtolower($metaRobots),
                sprintf(
                    'Url %s should not be noindex: %s',
                    $this->getCurrentUrl(),
                    $metaRobotsElement->getOuterHtml()
                )
            );

            Assert::assertNotContains(
                'nofollow',
                strtolower($metaRobots),
                sprintf(
                    'Url %s should not have meta robots with nofollow value: %s',
                    $this->getCurrentUrl(),
                    $metaRobotsElement->getOuterHtml()
                )
            );
        }

        $robotsHeaderTag = $this->getSession()->getResponseHeader('X-Robots-Tag');

        if (null != $robotsHeaderTag) {
            Assert::assertNotContains(
                'noindex',
                strtolower($robotsHeaderTag),
                sprintf(
                    'Url %s should not send X-Robots-Tag HTTP header with noindex value: %s',
                    $this->getCurrentUrl(),
                    $robotsHeaderTag
                )
            );

            Assert::assertNotContains(
                'nofollow',
                strtolower($robotsHeaderTag),
                sprintf(
                    'Url %s should not send X-Robots-Tag HTTP header with nofollow value: %s',
                    $this->getCurrentUrl(),
                    $robotsHeaderTag
                )
            );
        }

        $this->robotsContext->iShouldBeAbleToCrawl($this->getCurrentUrl());
    }

    /**
     * @param $expectedCanonicalUrl
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
     * @throws ServerException
     *
     * @Then the page HTML markup should be valid
     */
    public function thePageHtmlMarkupShouldBeValid()
    {
        $validator = new Validator();
        $pageHtmlContent = $this->getSession()->getPage()->getContent();

        /** @var Response $validatorResult */
        $validatorResult = $validator->validateDocument($pageHtmlContent);
        /** @var Message[] $htmlErrors */
        $htmlErrors = $validatorResult->getErrors();

        if (isset($htmlErrors[0])) {
            throw new \Exception(
                sprintf(
                    'HTML markup validation error: Line %s: "%s" - %s in %s',
                    $htmlErrors[0]->getFirstLine(),
                    $htmlErrors[0]->getExtract(),
                    $htmlErrors[0]->getText(),
                    $this->getCurrentUrl()
                )
            );
        }
    }

    /**
     * @param $expectedMetaTitle
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
