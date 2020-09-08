@UX
Feature: User Experience features

  Scenario: Testing responsive design
    Given I am on "/ux/site-with-valid-viewport.html"
    Then the site should be responsive

    When I am on "/ux/site-without-valid-viewport.html"
    Then the site should not be responsive

    When I am on "/ux/site-with-noviewport-tag.html"
    Then the site should not be responsive
