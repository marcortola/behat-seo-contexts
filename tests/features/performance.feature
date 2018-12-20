@PERFORMANCE
Feature: Performance feature

  Scenario: Testing HTML minification
    Given I am on "/performance/html/minified.html"
    Then html should be minimized

    When I am on "/performance/html/expanded.html"
    Then html should not be minimized

  Scenario: Testing CSS minification
    Given I am on "/performance/css/minified.html"
    Then css should be minimized

    When I am on "/performance/css/expanded.html"
    Then css should not be minimized

  Scenario: Testing JS minification
    Given I am on "/performance/js/minified.html"
    Then js should be minimized

    When I am on "/performance/js/expanded.html"
    Then js should not be minimized

  Scenario: Testing critical CSS
    Given I am on "/performance/css/with-critical-css.html"
    Then critical css should exist in head

    When I am on "/performance/css/minified.html"
    Then critical css should not exist in head

  Scenario: Testing browser cache
    Given I am on "/performance/cache/enabled-browser-cache.html"
    Then browser cache must be enabled for css resources

    When I am on "/performance/cache/disabled-browser-cache.html"
    Then browser cache must not be enabled for css resources

  Scenario: Testing JS loading async or defer
    Given I am on "/performance/js/async.html"
    Then js should load async
    And js should load defer

    When I am on "/performance/js/sync.html"
    Then js should not load async
    And js should not load defer
