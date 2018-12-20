@LOCALIZATION
Feature: Localization feature

  Scenario: Testing hreflang markup
    Given I am on "/localization/hreflang/valid-hreflang-en.html"
    Then the page hreflang markup should be valid

    When I am on "/localization/hreflang/valid-hreflang-es.html"
    Then the page hreflang markup should be valid

    When I am on "/localization/hreflang/not-valid-reciprocal-hreflang-en.html"
    Then the page hreflang markup should not be valid

    When I am on "/localization/hreflang/not-valid-reciprocal-hreflang-es.html"
    Then the page hreflang markup should not be valid

    When I am on "/localization/hreflang/not-valid-xdefault-hreflang-en.html"
    Then the page hreflang markup should not be valid

    When I am on "/localization/hreflang/not-coherent-xdefault-hreflang-en.html"
    Then the page hreflang markup should not be valid

    When I am on "/localization/hreflang/not-coherent-reciprocal-hreflang-es.html"
    Then the page hreflang markup should not be valid

    When I am on "/localization/hreflang/no-self-reference-hreflang-en.html"
    Then the page hreflang markup should not be valid

    When I am on "/localization/hreflang/no-self-reference-hreflang-es.html"
    Then the page hreflang markup should not be valid

    When I am on "/localization/hreflang/wrong-iso-code-hreflang-en.html"
    Then the page hreflang markup should not be valid

    When I am on "/localization/hreflang/wrong-iso-code-hreflang-es.html"
    Then the page hreflang markup should not be valid