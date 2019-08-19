@HTML
Feature: HTML feature

  Scenario: Testing HTML markup
    Given I am on "/html/valid-html.html"
    Then the page HTML markup should be valid

    When I am on "/html/not-valid-html.html"
    Then the page HTML markup should not be valid
 
    When I am on "/html/valid-html5-doctype-declaration.html"
    Then the page HTML5 doctype declaration should be valid

    When I am on "/html/not-valid-html5-doctype-declaration.html"
    Then the page HTML5 doctype declaration should not be valid
