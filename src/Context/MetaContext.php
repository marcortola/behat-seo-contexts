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
        $this->assertCanonicalElementExists();

        Assert::assertEquals(
            $this->toAbsoluteUrl($expectedCanonicalUrl),
            $this->getCanonicalElement()->getAttribute('href'),
            sprintf('Canonical url should be "%s"', $this->toAbsoluteUrl($expectedCanonicalUrl))
        );
    }
    
    /**
     * @throws \Exception
     *
     * @Then the page canonical should not be empty
     */
    public function thePageCanonicalShouldNotBeEmpty()
    {
        $this->assertCanonicalElementExists();

        Assert::assertNotEmpty(
            trim($this->getCanonicalElement()->getAttribute('href')),
            'Canonical url is empty'
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
     * @throws \Exception     
     */
    private function assertCanonicalElementExists()
    {
        Assert::assertNotNull(
            $this->getCanonicalElement(),
            'Canonical element does not exist'
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
     * @Then the page title should be :expectedTitle
     */
    public function thePageTitleShouldBe(string $expectedTitle)
    {
        $this->assertTitleElementExists();

        Assert::assertEquals(
            $expectedTitle,
            $this->getTitleElement()->getText(),
            sprintf(
                'Title tag is not "%s"',
                $expectedTitle
            )
        );
    }

    /**
     * @throws \Exception
     *
     * @Then the page title should not be empty
     */
    public function thePageTitleShouldNotBeEmpty()
    {
        $this->assertTitleElementExists();

        Assert::assertNotEmpty(
            trim($this->getTitleElement()->getText()),
            'Title tag is empty'
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
     */
    private function assertTitleElementExists()
    {
        Assert::assertNotNull(
            $this->getTitleElement(),
            'Title tag does not exist'
        );
    }

    /**
     * @throws \Exception
     *
     * @Then the page meta description should be :expectedMetaDescription
     */
    public function thePageMetaDescriptionShouldBe(string $expectedMetaDescription)
    {
        $this->assertPageMetaDescriptionElementExists();

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
     * @throws \Exception
     *
     * @Then the page meta description should not be empty
     */
    public function thePageMetaDescriptionNotBeEmpty()
    {
        $this->assertPageMetaDescriptionElementExists();

        Assert::assertNotEmpty(
            trim($this->getMetaDescriptionElement()->getAttribute('content')),
            'Meta description is empty'
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
     * @throws \Exception     
     */
    private function assertPageMetaDescriptionElementExists()
    {
        Assert::assertNotNull(
            $this->getMetaDescriptionElement(),
            'Meta description does not exist'
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
     * @Then the page title should not exist
     */
    public function thePageTitleShouldNotExist()
    {
        Assert::assertNull(
            $this->getTitleElement(),
            'Title tag does exist.'
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
