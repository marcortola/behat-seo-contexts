<?php

declare(strict_types=1);

namespace MarcOrtola\BehatSEOContexts\Context;

use Behat\Mink\Element\NodeElement;
use Webmozart\Assert\Assert;

class UXContext extends BaseContext
{
    public const EXPECTED_VIEWPORT = "width=device-width, initial-scale=1";

    /**
     * @Then the site should be responsive
     */
    public function theSiteShouldBeResponsive(): void
    {
        $viewportContent = $this->getViewportElement()->getAttribute('content');

        Assert::eq(
            self::EXPECTED_VIEWPORT,
            $viewportContent,
            'Site does not support responsive design'
        );
    }

    /**
     * @Then the site should not be responsive
     */
    public function theSiteShouldNotBeResponsive(): void
    {
        try {
            $viewportContent = $this->getViewportElement()->getAttribute('content');
        } catch (\InvalidArgumentException $e) {
            return;
        }

        Assert::notEq(
            self::EXPECTED_VIEWPORT,
            $viewportContent,
            'Site supports responsive design'
        );
    }

    private function getViewportElement(): NodeElement
    {
        $viewportElement = $this->getSession()->getPage()->find(
            'xpath',
            '//head/meta[@name="viewport"]'
        );

        Assert::notNull($viewportElement);

        return $viewportElement;
    }
}
