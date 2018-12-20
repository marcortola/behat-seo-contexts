@SITEMAP
Feature: Sitemap feature

  Scenario: Testing standard sitemap
    Given the sitemap "/sitemap/standard-sitemap.xml"
    Then the sitemap should be a valid sitemap
    And the sitemap has 5 children
    And the sitemap URLs are alive

    When the sitemap "/sitemap/invalid-standard-sitemap.xml"
    Then the sitemap should not be a valid sitemap

  Scenario: Testing index sitemap
    Given the sitemap "/sitemap/index-sitemap.xml"
    Then the sitemap should be a valid index sitemap
    And the index sitemap should have child "http://localhost:8080/sitemap/standard-sitemap.xml"
    And the sitemap has 2 children
    And the sitemap URLs are alive

    When the sitemap "/sitemap/invalid-index-sitemap.xml"
    Then the sitemap should not be a valid index sitemap

  Scenario: Testing multilanguage sitemap
    Given the sitemap "/sitemap/multilanguage-sitemap.xml"
    Then the sitemap should be a valid multilanguage sitemap
    And the multilanguage sitemap pass Google validation
    And the sitemap has 3 children
    And the sitemap URLs are alive

    When the sitemap "/sitemap/invalid-multilanguage-sitemap.xml"
    Then the sitemap should not be a valid multilanguage sitemap

    When the sitemap "/sitemap/invalid-google-multilanguage-sitemap.xml"
    Then the multilanguage sitemap should not pass Google validation
