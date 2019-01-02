@PERFORMANCE
Feature: Performance feature

  Scenario: Testing HTML minification
    Given I am on "/performance/html/minified.html"
    Then HTML code should be minified

    When I am on "/performance/html/expanded.html"
    Then HTML code should not be minified

  Scenario: Testing CSS minification
    Given I am on "/performance/css/minified.html"
    Then CSS code should be minified

    When I am on "/performance/css/expanded.html"
    Then CSS code should not be minified

  Scenario: Testing JS minification
    Given I am on "/performance/js/minified.html"
    Then Javascript code should be minified

    When I am on "/performance/js/expanded.html"
    Then Javascript code should not be minified

  Scenario: Testing critical CSS
    Given I am on "/performance/css/with-critical-css.html"
    Then critical CSS code should exist in head

    When I am on "/performance/css/minified.html"
    Then critical CSS code should not exist in head

  Scenario: Testing browser cache
    Given I am on "/performance/cache/enabled-browser-cache.html"
    Then browser cache should be enabled for internal css resources

    When I am on "/performance/cache/disabled-browser-cache.html"
    Then browser cache should not be enabled for internal css resources

    When I am on "/performance/cache/enabled-external-browser-cache.html"
    Then browser cache should be enabled for external css resources

    When I am on "/performance/cache/enabled-external-browser-cache.html"
    Then browser cache should be enabled for https://stackpath.bootstrapcdn.com css resources

  Scenario: Testing JS loading async or defer
    Given I am on "/performance/js/async.html"
    Then Javascript code should load async
    And Javascript code should load defer

    When I am on "/performance/js/sync.html"
    Then Javascript code should not load async
    And Javascript code should not load defer
