@ACCESSIBILITY
Feature: Accessibility features

  Scenario: Testing image alt tag
    Given I am on homepage
    Then the images should have alt text

    When I am on "/accessibility/image-with-alt-text.html"
    Then the images should have alt text
