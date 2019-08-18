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
