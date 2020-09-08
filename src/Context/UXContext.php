<?php declare(strict_types=1);

namespace MarcOrtola\BehatSEOContexts\Context;

use Behat\Mink\Element\NodeElement;
use Webmozart\Assert\Assert;

class UXContext extends BaseContext
{
  /**
   * @Then the site should be responsive
   */
  public function theSiteShouldBeResponsive(): void
  {

      $viewportElement = $this->getViewportElement();

      Assert::notNull($viewportElement, 'Site does not support responsive design');
      $expectedViewportContent = "width=device-width, initial-scale=1";

      $viewportContent = $viewportElement->getAttribute('content');
      Assert::eq(
          $expectedViewportContent,
          $viewportContent,
          'Site does not support responsive design'
      );
  }

  /**
   * @Then the site should not be responsive
   */
  public function theSiteShouldNotBeResponsive(): void
  {
      $viewportElement = $this->getViewportElement();

      Assert::null($viewportElement);
      $expectedViewportContent = "width=device-width, initial-scale=1";

      $viewportContent = $viewportElement->getAttribute('content');
      Assert::notEq(
          $expectedViewportContent,
          $viewportContent,
          'Site supports responsive design'
      );
  }

  private function getViewportElement(): ?NodeElement
  {
      return $this->getSession()->getPage()->find(
          'xpath',
          '//head/meta[@name="viewport"]'
        );
  }
}
