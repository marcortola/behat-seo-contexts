<?php declare(strict_types=1);

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Mink\Element\NodeElement;
use Webmozart\Assert\Assert;

class MetaContext extends BaseContext
{
    /**
     * @Then the page canonical should be :expectedCanonicalUrl
     */
    public function thePageCanonicalShouldBe(string $expectedCanonicalUrl): void
    {
        $this->assertCanonicalElementExists();

        $canonicalElement = $this->getCanonicalElement();

        Assert::notNull($canonicalElement);

        Assert::eq(
            $this->toAbsoluteUrl($expectedCanonicalUrl),
            $canonicalElement->getAttribute('href'),
            sprintf('Canonical url should be "%s"', $this->toAbsoluteUrl($expectedCanonicalUrl))
        );
    }

    private function assertCanonicalElementExists(): void
    {
        Assert::notNull(
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
     * @Then the page canonical should not be empty
     */
    public function thePageCanonicalShouldNotBeEmpty(): void
    {
        $this->assertCanonicalElementExists();

        $canonicalElement = $this->getCanonicalElement();

        Assert::notNull($canonicalElement);

        Assert::notEmpty(
            trim($canonicalElement->getAttribute('href') ?? ''),
            'Canonical url is empty'
        );
    }

    /**
     * @Then the page meta robots should be noindex
     */
    public function thePageShouldBeNoindex(): void
    {
        $metaRobotsElement = $this->getMetaRobotsElement();

        Assert::notNull(
            $metaRobotsElement,
            'Meta robots does not exist.'
        );

        Assert::contains(
            strtolower($metaRobotsElement->getAttribute('content') ?? ''),
            'noindex',
            sprintf(
                'Url %s is not noindex: %s',
                $this->getCurrentUrl(),
                $metaRobotsElement->getHtml()
            )
        );
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
     * @Then /^the page title should be "(?P<expectedTitle>[^"]*)"$/
     */
    public function thePageTitleShouldBe(string $expectedTitle): void
    {
        $this->assertTitleElementExists();

        $titleElement = $this->getTitleElement();

        Assert::notNull($titleElement);

        Assert::eq(
            $expectedTitle,
            $titleElement->getText(),
            sprintf(
                'Title tag is not "%s"',
                $expectedTitle
            )
        );
    }

    private function assertTitleElementExists(): void
    {
        Assert::notNull(
            $this->getTitleElement(),
            'Title tag does not exist'
        );
    }

    private function getTitleElement(): ?NodeElement
    {
        return $this->getSession()->getPage()->find('css', 'title');
    }

    /**
     * @Then the page title should be empty
     */
    public function thePageTitleShouldBeEmpty(): void
    {
        $this->assertTitleElementExists();

        $titleElement = $this->getTitleElement();

        Assert::notNull($titleElement);

        Assert::isEmpty(
            trim($titleElement->getText()),
            'Title tag is not empty'
        );
    }

    /**
     * @Then the page title should not be empty
     */
    public function thePageTitleShouldNotBeEmpty(): void
    {
        $this->assertTitleElementExists();

        $titleElement = $this->getTitleElement();

        Assert::notNull($titleElement);

        Assert::notEmpty(
            trim($titleElement->getText()),
            'Title tag is empty'
        );
    }

    /**
     * @Then /^the page meta description should be "(?P<expectedMetaDescription>[^"]*)"$/
     */
    public function thePageMetaDescriptionShouldBe(string $expectedMetaDescription): void
    {
        $this->assertPageMetaDescriptionElementExists();

        $metaDescription = $this->getMetaDescriptionElement();

        Assert::notNull($metaDescription);

        Assert::eq(
            $expectedMetaDescription,
            $metaDescription->getAttribute('content'),
            sprintf(
                'Meta description is not "%s"',
                $expectedMetaDescription
            )
        );
    }

    private function assertPageMetaDescriptionElementExists(): void
    {
        Assert::notNull(
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
     * @Then the page meta description should be empty
     */
    public function thePageMetaDescriptionBeEmpty(): void
    {
        $this->assertPageMetaDescriptionElementExists();

        $metaDescription = $this->getMetaDescriptionElement();

        Assert::notNull($metaDescription);

        Assert::isEmpty(
            trim($metaDescription->getAttribute('content') ?? ''),
            'Meta description is not empty'
        );
    }

    /**
     * @Then the page meta description should not be empty
     */
    public function thePageMetaDescriptionNotBeEmpty(): void
    {
        $this->assertPageMetaDescriptionElementExists();

        $metaDescription = $this->getMetaDescriptionElement();

        Assert::notNull($metaDescription);

        Assert::notEmpty(
            trim($metaDescription->getAttribute('content') ?? ''),
            'Meta description is empty'
        );
    }

    /**
     * @Then the page canonical should not exist
     */
    public function thePageCanonicalShouldNotExist(): void
    {
        Assert::null(
            $this->getCanonicalElement(),
            'Canonical does exist'
        );
    }

    /**
     * @Then the page title should not exist
     */
    public function thePageTitleShouldNotExist(): void
    {
        Assert::null(
            $this->getTitleElement(),
            'Title tag does exist.'
        );
    }

    /**
     * @Then the page meta description should not exist
     */
    public function thePageMetaDescriptionShouldNotExist(): void
    {
        Assert::null(
            $this->getMetaDescriptionElement(),
            'Meta description does exist'
        );
    }
}
