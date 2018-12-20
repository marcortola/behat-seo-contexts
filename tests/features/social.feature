@SOCIAL
Feature: Social feature

  Scenario: Testing Twitter Open Graph data
    Given I am on "/social/valid-og-full-requirements.html"
    Then the twitter open graph data should satisfy minimum requirements
    And the twitter open graph data should satisfy full requirements

    When I am on "/social/valid-og-minimum-requirements.html"
    Then the twitter open graph data should satisfy minimum requirements

    Given I am on "/social/without-og-data.html"
    Then the twitter open graph data should not satisfy minimum requirements
    And the twitter open graph data should not satisfy full requirements

    When I am on "/social/valid-og-minimum-requirements.html"
    Then the twitter open graph data should not satisfy full requirements

  Scenario: Testing Facebook Open Graph data
    Given I am on "/social/valid-og-full-requirements.html"
    Then the facebook open graph data should satisfy minimum requirements
    And the facebook open graph data should satisfy full requirements

    When I am on "/social/valid-og-minimum-requirements.html"
    Then the facebook open graph data should satisfy minimum requirements

    Given I am on "/social/without-og-data.html"
    Then the facebook open graph data should not satisfy minimum requirements
    And the facebook open graph data should not satisfy full requirements

    When I am on "/social/valid-og-minimum-requirements.html"
    Then the facebook open graph data should not satisfy full requirements