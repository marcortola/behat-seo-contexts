@REDIRECT
Feature: Redirect feature

  Scenario: Testing redirects
    Given I do not follow redirects
    When I am on "/redirect/redirect.php"
    Then the response status code should be 301
    And I should be redirected to "/redirect/final.php"

    When I am on "/redirect/final.php"
    Then the response status code should be 200