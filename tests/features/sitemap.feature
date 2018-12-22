@SITEMAP
Feature: Sitemap feature

  Scenario: Testing standard sitemap
    Given the sitemap "/sitemap/standard-sitemap.xml"
    Then the sitemap should be valid
    And the sitemap should have 5 children
    And the sitemap URLs should be alive

    When the sitemap "/sitemap/invalid-standard-sitemap.xml"
    Then the sitemap should not be valid

  Scenario: Testing index sitemap
    Given the sitemap "/sitemap/index-sitemap.xml"
    Then the index sitemap should be valid
    And the index sitemap should have a child with URL "http://localhost:8080/sitemap/standard-sitemap.xml"
    And the sitemap should have 2 children
    And the sitemap URLs should be alive

    When the sitemap "/sitemap/invalid-index-sitemap.xml"
    Then the index sitemap should not be valid

  Scenario: Testing multilanguage sitemap
    Given the sitemap "/sitemap/multilanguage-sitemap.xml"
    Then the multilanguage sitemap should be valid
    And the multilanguage sitemap should pass Google validation
    And the sitemap should have 3 children
    And the sitemap URLs should be alive

    When the sitemap "/sitemap/invalid-multilanguage-sitemap.xml"
    Then the multilanguage sitemap should not be valid

    When the sitemap "/sitemap/invalid-google-multilanguage-sitemap.xml"
    Then the multilanguage sitemap should not pass Google validation
