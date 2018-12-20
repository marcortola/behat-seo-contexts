@META
Feature: Meta feature

  Scenario: Testing page canonical
    Given I am on "/meta/with-seo-meta.html"
    Then the page canonical should be "http://localhost:8080/meta/with-seo-meta.html"

    When I am on "/meta/without-seo-meta.html"
    Then the page canonical should not exist

  Scenario: Testing page meta title
    Given I am on "/meta/with-seo-meta.html"
    Then the page meta title should be "Test title"

    When I am on "/meta/with-empty-seo-meta.html"
    Then the page meta title should be ""

    When I am on "/meta/without-seo-meta.html"
    Then the page meta title should not exist

  Scenario: Testing page meta description
    Given I am on "/meta/with-seo-meta.html"
    Then the page meta description should be "Test description"

    When I am on "/meta/with-empty-seo-meta.html"
    Then the page meta description should be ""

    When I am on "/meta/without-seo-meta.html"
    Then the page meta description should not exist
