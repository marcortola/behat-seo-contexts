<?php declare(strict_types=1);

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
        $imageElements = $this->getImageElement();

        foreach($imageElements as $imageElement)
        {
          Assert::notNull($imageElement);
          $imageAlt = $imageElement->getAttribute('alt');
          Assert::notEmpty($imageAlt,'Alt Text is empty for image: ' + $imageElement);
        }
    }

    private function getImageElement(): ?NodeElement
    {
        return $this->getSession()->getPage()->find('css', 'img');
    }
}
