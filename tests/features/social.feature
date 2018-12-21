@SOCIAL
Feature: Social feature

  Scenario: Testing Twitter Open Graph data
    Given I am on "/social/valid-og-full-requirements.html"
    Then the Twitter Open Graph data should satisfy minimum requirements
    And the Twitter Open Graph data should satisfy full requirements

    When I am on "/social/valid-og-minimum-requirements.html"
    Then the Twitter Open Graph data should satisfy minimum requirements

    Given I am on "/social/without-og-data.html"
    Then the Twitter Open Graph data should not satisfy minimum requirements
    And the Twitter Open Graph data should not satisfy full requirements

    When I am on "/social/valid-og-minimum-requirements.html"
    Then the Twitter Open Graph data should not satisfy full requirements

  Scenario: Testing Facebook Open Graph data
    Given I am on "/social/valid-og-full-requirements.html"
    Then the Facebook Open Graph data should satisfy minimum requirements
    And the Facebook Open Graph data should satisfy full requirements

    When I am on "/social/valid-og-minimum-requirements.html"
    Then the Facebook Open Graph data should satisfy minimum requirements

    Given I am on "/social/without-og-data.html"
    Then the Facebook Open Graph data should not satisfy minimum requirements
    And the Facebook Open Graph data should not satisfy full requirements

    When I am on "/social/valid-og-minimum-requirements.html"
    Then the Facebook Open Graph data should not satisfy full requirements