@INDEXATION
Feature: Robots feature

  Scenario: Testing page indexability status
    Given I am on "/indexation/meta-robots-index.html"
    Then the page should be indexable

    When I am on "/indexation/meta-robots-index-x-robots-tag-header-index.php"
    Then the page should be indexable

    When I am on "/indexation/meta-robots-index-x-robots-tag-header-noindex.php"
    Then the page should not be indexable

    When I am on "/indexation/meta-robots-noindex.html"
    Then the page should not be indexable

    When I am on "/indexation/meta-robots-noindex-x-robots-tag-header-index.php"
    Then the page should not be indexable

    When I am on "/indexation/meta-robots-noindex-x-robots-tag-header-noindex.php"
    Then the page should not be indexable

    When I am on "/indexation/without-index-blockers.html"
    Then the page should be indexable

    When I am on "/indexation/x-robots-tag-header-index.php"
    Then the page should be indexable

    When I am on "/indexation/x-robots-tag-header-noindex.php"
    Then the page should not be indexable

    When I am on "/indexation/blocked-by-robots.php"
    Then the page should not be indexable
