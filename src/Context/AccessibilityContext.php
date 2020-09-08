<?php

declare(strict_types=1);

namespace MarcOrtola\BehatSEOContexts\Context;

use Behat\Mink\Element\NodeElement;
use Webmozart\Assert\Assert;

class AccessibilityContext extends BaseContext
{
    /**
     * @Then the images should have alt text
     */
    public function theImagesShouldHaveAltText(): void
    {
        foreach ($this->getImageElements() as $imageElement) {
            Assert::notEmpty(
                $imageElement->getAttribute('alt'),
                'Alt Text is empty for image: ' . $imageElement->getHtml()
            );
        }
    }

    /**
     * @return NodeElement[]
     */
    private function getImageElements(): array
    {
        return $this->getSession()->getPage()->findAll('css', 'img');
    }
}
