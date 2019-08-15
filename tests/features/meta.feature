@META
Feature: Meta feature

  Scenario: Testing page canonical
    Given I am on "/meta/with-seo-meta.html"
    Then the page canonical should be "http://localhost:8080/meta/with-seo-meta.html"

    When I am on "/meta/with-seo-meta.html"
    Then the page canonical should not be empty

    When I am on "/meta/without-seo-meta.html"
    Then the page canonical should not exist

  Scenario: Testing page meta title
    Given I am on "/meta/with-seo-meta.html"
    Then the page title should be "Test title"

    When I am on "/meta/with-seo-meta.html"
    Then the page title should not be empty

    When I am on "/meta/with-empty-seo-meta.html"
    Then the page title should be ""

    When I am on "/meta/without-seo-meta.html"
    Then the page title should not exist

  Scenario: Testing page meta description
    Given I am on "/meta/with-seo-meta.html"
    Then the page meta description should be "Test description"

    When I am on "/meta/with-seo-meta.html"
    Then the page meta description should not be empty

    When I am on "/meta/with-empty-seo-meta.html"
    Then the page meta description should be ""

    When I am on "/meta/without-seo-meta.html"
    Then the page meta description should not exist

  Scenario: Testing page meta robots

    Given I am on "/meta/robots/bad-meta-robots.html"
    Then the page meta robots should not be noindex

    When I am on "/meta/robots/meta-robots-index.html"
    Then the page meta robots should not be noindex

    When I am on "/meta/robots/meta-robots-index-follow.html"
    Then the page meta robots should not be noindex

    When I am on "/meta/robots/meta-robots-index-nofollow.html"
    Then the page meta robots should not be noindex

    When I am on "/meta/robots/meta-robots-noindex.html"
    Then the page meta robots should be noindex

    When I am on "/meta/robots/meta-robots-noindex-follow.html"
    Then the page meta robots should be noindex

    When I am on "/meta/robots/meta-robots-noindex-nofollow.html"
    Then the page meta robots should be noindex

    When I am on "/meta/robots/without-meta-robots.html"
    Then the page meta robots should not be noindex    