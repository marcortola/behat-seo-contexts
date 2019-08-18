@HTML
Feature: HTML feature

  Scenario: Testing HTML markup
    Given I am on "/html/valid-html.html"
    Then the page HTML markup should be valid

    When I am on "/html/not-valid-html.html"
    Then the page HTML markup should not be valid
