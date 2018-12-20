@ROBOTS
Feature: Robots feature

  Scenario: Testing robots.txt block
    Given I am a "Googlebot" crawler
    Then I should be able to crawl "/test"
    And I should be able to crawl "/only-google/test"
    And I should not be able to crawl "/test?v=2"

    When I am a "RandomBot" crawler
    Then I should be able to crawl "/test"
    And I should not be able to crawl "/only-google/test"
    And I should not be able to crawl "/test?v=2"

    When I am a "BadBot" crawler
    Then I should not be able to crawl "/"
    Then I should not be able to crawl "/test"
    And I should not be able to crawl "/only-google/test"
    And I should not be able to crawl "/test?v=2"

  Scenario: Testing sitemap URL in robots.txt
    Given I am a "RandomBot" crawler
    Then I should be able to get the sitemap URL

  Scenario: Testing page meta robots noindex
    Given I am on "/robots/bad-meta-robots.html"
    Then the page should not be noindex

    When I am on "/robots/meta-robots-index.html"
    Then the page should not be noindex

    When I am on "/robots/meta-robots-index-follow.html"
    Then the page should not be noindex

    When I am on "/robots/meta-robots-index-nofollow.html"
    Then the page should not be noindex

    When I am on "/robots/meta-robots-noindex.html"
    Then the page should be noindex

    When I am on "/robots/meta-robots-noindex-follow.html"
    Then the page should be noindex

    When I am on "/robots/meta-robots-noindex-nofollow.html"
    Then the page should be noindex

    When I am on "/robots/without-meta-robots.html"
    Then the page should not be noindex