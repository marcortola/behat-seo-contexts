<?php

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Mink\Element\NodeElement;
use PHPUnit\Framework\Assert;

class MetaContext extends BaseContext
{
    /**
     * @throws \Exception
     *
     * @Then the page canonical should be :expectedCanonicalUrl
     */
    public function thePageCanonicalShouldBe(string $expectedCanonicalUrl)
    {
        Assert::assertNotNull(
            $this->getCanonicalElement(),
            'Canonical element does not exist'
        );

        Assert::assertEquals(
            $this->toAbsoluteUrl($expectedCanonicalUrl),
            $this->getCanonicalElement()->getAttribute('href'),
            sprintf('Canonical url should be "%s"', $this->toAbsoluteUrl($expectedCanonicalUrl))
        );
    }

    /**
     * @return NodeElement|null
     */
    private function getCanonicalElement()
    {
        return $this->getSession()->getPage()->find(
            'xpath',
            '//head/link[@rel="canonical"]'
        );
    }

    /**
     * @Then the page meta robots should be noindex
     */
    public function thePageShouldBeNoindex()
    {
        Assert::assertNotNull(
            $this->getMetaRobotsElement(),
            'Meta robots does not exist.'
        );

        Assert::assertContains(
            'noindex',
            strtolower($this->getMetaRobotsElement()->getAttribute('content')),
            sprintf(
                'Url %s is not noindex: %s',
                $this->getCurrentUrl(),
                $this->getMetaRobotsElement()->getOuterHtml()
            )
        );
    }

    /**
     * @return NodeElement|null
     */
    private function getMetaRobotsElement()
    {
        return $this->getSession()->getPage()->find(
            'xpath',
            '//head/meta[@name="robots"]'
        );
    }

    /**
     * @Then the page meta robots should not be noindex
     */
    public function thePageShouldNotBeNoindex()
    {
        $this->assertInverse(
            [$this, 'thePageShouldBeNoindex'],
            'Page meta robots is noindex.'
        );
    }

    /**
     * @throws \Exception
     *
     * @Then the page meta title should be :expectedMetaTitle
     */
    public function thePageMetaTitleShouldBe(string $expectedMetaTitle)
    {
        Assert::assertNotNull(
            $this->getTitleElement(),
            'Meta title does not exist'
        );

        Assert::assertEquals(
            $expectedMetaTitle,
            $this->getTitleElement()->getText(),
            sprintf(
                'Meta title is not "%s"',
                $expectedMetaTitle
            )
        );
    }

    /**
     * @return NodeElement|null
     */
    private function getTitleElement()
    {
        return $this->getSession()->getPage()->find('css', 'title');
    }

    /**
     * @throws \Exception
     *
     * @Then the page meta description should be :expectedMetaDescription
     */
    public function thePageMetaDescriptionShouldBe(string $expectedMetaDescription)
    {
        Assert::assertNotNull(
            $this->getMetaDescriptionElement(),
            'Meta description does not exist'
        );

        Assert::assertEquals(
            $expectedMetaDescription,
            $this->getMetaDescriptionElement()->getAttribute('content'),
            sprintf(
                'Meta description is not "%s"',
                $expectedMetaDescription
            )
        );
    }

    /**
     * @return NodeElement|null
     */
    private function getMetaDescriptionElement()
    {
        return $this->getSession()->getPage()->find(
            'xpath',
            '//head/meta[@name="description"]'
        );
    }

    /**
     * @Then the page canonical should not exist
     */
    public function thePageCanonicalShouldNotExist()
    {
        Assert::assertNull(
            $this->getCanonicalElement(),
            'Canonical does exist'
        );
    }

    /**
     * @Then the page meta title should not exist
     */
    public function thePageMetaTitleShouldNotExist()
    {
        Assert::assertNull(
            $this->getTitleElement(),
            'Title does exist'
        );
    }

    /**
     * @Then the page meta description should not exist
     */
    public function thePageMetaDescriptionShouldNotExist()
    {
        Assert::assertNull(
            $this->getMetaDescriptionElement(),
            'Meta description does exist'
        );
    }
}
