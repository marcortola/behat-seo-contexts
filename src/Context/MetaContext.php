<?php declare(strict_types=1);

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Mink\Element\NodeElement;
use Exception;
use PHPUnit\Framework\Assert;

class MetaContext extends BaseContext
{
    /**
     * @throws Exception
     *
     * @Then the page canonical should be :expectedCanonicalUrl
     */
    public function thePageCanonicalShouldBe(string $expectedCanonicalUrl): void
    {
        $this->assertCanonicalElementExists();

        if ($canonicalElement = $this->getCanonicalElement()) {
            Assert::assertEquals(
                $this->toAbsoluteUrl($expectedCanonicalUrl),
                $canonicalElement->getAttribute('href'),
                sprintf('Canonical url should be "%s"', $this->toAbsoluteUrl($expectedCanonicalUrl))
            );
        }
    }

    /**
     * @throws Exception
     */
    private function assertCanonicalElementExists(): void
    {
        Assert::assertNotNull(
            $this->getCanonicalElement(),
            'Canonical element does not exist'
        );
    }

    private function getCanonicalElement(): ?NodeElement
    {
        return $this->getSession()->getPage()->find(
            'xpath',
            '//head/link[@rel="canonical"]'
        );
    }

    /**
     * @throws Exception
     *
     * @Then the page canonical should not be empty
     */
    public function thePageCanonicalShouldNotBeEmpty(): void
    {
        $this->assertCanonicalElementExists();

        if ($canonicalElement = $this->getCanonicalElement()) {
            Assert::assertNotEmpty(
                trim($canonicalElement->getAttribute('href') ?? ''),
                'Canonical url is empty'
            );
        }
    }

    /**
     * @Then the page meta robots should be noindex
     */
    public function thePageShouldBeNoindex(): void
    {
        $metaRobotsElement = $this->getMetaRobotsElement();

        Assert::assertNotNull(
            $metaRobotsElement,
            'Meta robots does not exist.'
        );

        if ($metaRobotsElement) {
            Assert::assertContains(
                'noindex',
                strtolower($metaRobotsElement->getAttribute('content') ?? ''),
                sprintf(
                    'Url %s is not noindex: %s',
                    $this->getCurrentUrl(),
                    $this->getOuterHtml($metaRobotsElement)
                )
            );
        }
    }

    private function getMetaRobotsElement(): ?NodeElement
    {
        return $this->getSession()->getPage()->find(
            'xpath',
            '//head/meta[@name="robots"]'
        );
    }

    /**
     * @Then the page meta robots should not be noindex
     */
    public function thePageShouldNotBeNoindex(): void
    {
        $this->assertInverse(
            [$this, 'thePageShouldBeNoindex'],
            'Page meta robots is noindex.'
        );
    }

    /**
     * @throws Exception
     *
     * @Then /^the page title should be "(?P<expectedTitle>[^"]*)"$/
     */
    public function thePageTitleShouldBe(string $expectedTitle): void
    {
        $this->assertTitleElementExists();

        if ($titleElement = $this->getTitleElement()) {
            Assert::assertEquals(
                $expectedTitle,
                $titleElement->getText(),
                sprintf(
                    'Title tag is not "%s"',
                    $expectedTitle
                )
            );
        }
    }

    /**
     * @throws Exception
     */
    private function assertTitleElementExists(): void
    {
        Assert::assertNotNull(
            $this->getTitleElement(),
            'Title tag does not exist'
        );
    }

    private function getTitleElement(): ?NodeElement
    {
        return $this->getSession()->getPage()->find('css', 'title');
    }

    /**
     * @throws Exception
     *
     * @Then the page title should be empty
     */
    public function thePageTitleShouldBeEmpty(): void
    {
        $this->assertTitleElementExists();

        if ($titleElement = $this->getTitleElement()) {
            Assert::assertEmpty(
                trim($titleElement->getText()),
                'Title tag is not empty'
            );
        }
    }

    /**
     * @throws Exception
     *
     * @Then the page title should not be empty
     */
    public function thePageTitleShouldNotBeEmpty(): void
    {
        $this->assertTitleElementExists();

        if ($titleElement = $this->getTitleElement()) {
            Assert::assertNotEmpty(
                trim($titleElement->getText()),
                'Title tag is empty'
            );
        }
    }

    /**
     * @throws Exception
     *
     * @Then /^the page meta description should be "(?P<expectedMetaDescription>[^"]*)"$/
     */
    public function thePageMetaDescriptionShouldBe(string $expectedMetaDescription): void
    {
        $this->assertPageMetaDescriptionElementExists();

        if ($metaDescription = $this->getMetaDescriptionElement()) {
            Assert::assertEquals(
                $expectedMetaDescription,
                $metaDescription->getAttribute('content'),
                sprintf(
                    'Meta description is not "%s"',
                    $expectedMetaDescription
                )
            );
        }
    }

    /**
     * @throws Exception
     */
    private function assertPageMetaDescriptionElementExists(): void
    {
        Assert::assertNotNull(
            $this->getMetaDescriptionElement(),
            'Meta description does not exist'
        );
    }

    private function getMetaDescriptionElement(): ?NodeElement
    {
        return $this->getSession()->getPage()->find(
            'xpath',
            '//head/meta[@name="description"]'
        );
    }

    /**
     * @throws Exception
     *
     * @Then the page meta description should be empty
     */
    public function thePageMetaDescriptionBeEmpty(): void
    {
        $this->assertPageMetaDescriptionElementExists();

        if ($metaDescription = $this->getMetaDescriptionElement()) {
            Assert::assertEmpty(
                trim($metaDescription->getAttribute('content') ?? ''),
                'Meta description is not empty'
            );
        }
    }

    /**
     * @throws Exception
     *
     * @Then the page meta description should not be empty
     */
    public function thePageMetaDescriptionNotBeEmpty(): void
    {
        $this->assertPageMetaDescriptionElementExists();

        if ($metaDescription = $this->getMetaDescriptionElement()) {
            Assert::assertNotEmpty(
                trim($metaDescription->getAttribute('content') ?? ''),
                'Meta description is empty'
            );
        }
    }

    /**
     * @Then the page canonical should not exist
     */
    public function thePageCanonicalShouldNotExist(): void
    {
        Assert::assertNull(
            $this->getCanonicalElement(),
            'Canonical does exist'
        );
    }

    /**
     * @Then the page title should not exist
     */
    public function thePageTitleShouldNotExist(): void
    {
        Assert::assertNull(
            $this->getTitleElement(),
            'Title tag does exist.'
        );
    }

    /**
     * @Then the page meta description should not exist
     */
    public function thePageMetaDescriptionShouldNotExist(): void
    {
        Assert::assertNull(
            $this->getMetaDescriptionElement(),
            'Meta description does exist'
        );
    }
}
