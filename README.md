# Behat SEO Contexts
Behat extension for testing some On-Page SEO factors.

Includes contexts for testing:
* meta title/description
* canonical
* hreflang
* meta robots
* robots.txt
* redirects
* sitemap validation (inc. multilanguage)
* HTML validation
* performance 
* more...

Installation
------------

Basic requirements:

* Php 5.6+
* Behat 3+
* Mink + Mink extension
* PHPUnit

### How to install it

1. [Install Composer](https://getcomposer.org/download/)
2. Execute:

```
$ composer require mortola/behat-seo-contexts --dev
```

3. Add the Context you need to `behat.yml`:

```yaml
# behat.yml
default:
    # ...
    suites:
        default:
          contexts:
            - MOrtola\BehatSEOContexts\Context\MetaContext
            - MOrtola\BehatSEOContexts\Context\LocalizationContext
            - MOrtola\BehatSEOContexts\Context\RobotsContext
            - MOrtola\BehatSEOContexts\Context\RedirectContext
            - MOrtola\BehatSEOContexts\Context\SitemapContext
            - MOrtola\BehatSEOContexts\Context\HTMLContext
            - MOrtola\BehatSEOContexts\Context\PerformanceContext
            - MOrtola\BehatSEOContexts\Context\SocialContext

```
### Featured steps
##### MetaContext
```
Then the page canonical should be :expectedCanonicalUrl
Then the page meta title should be :expectedMetaTitle
Then the page meta description should be :expectedMetaDescription
```
##### LocalizationContext
```
Then the page hreflang markup should be valid
```
##### RobotsContext
```
Then the page should not be noindex
Then I should not be able to crawl :resource
Then I should be able to crawl :resource
Then I should be able to get the sitemap URL
```
##### RedirectContext
```
Given /^I follow redirects$/
Given /^I do not follow redirects$/
Then /^I (?:am|should be) redirected(?: to "([^"]*)")?$/
```
##### SitemapContext
```
Given the sitemap :sitemapUrl
Then /^the sitemap should be a valid (index |multilanguage |)sitemap$/
Then the index sitemap should have child :childSitemapUrl
Then /^the sitemap has ([0-9]+) children$/
Then the multilanguage sitemap pass Google validation
Then the sitemap URLs are alive
```
##### HTMLContext
```
Then the page HTML markup should be valid
```
##### PerformanceContext
```
Then /^browser cache must be enabled for (png|jpeg|gif|ico|js|css) resources$/
Then /^js should load (async|defer)$/
Then /^html should be minimized$/
Then /^(css|js) should be minimized$/
Then css should load deferred
Then critical css should exist in head
```
##### SocialContext
```
Then /^the (twitter|facebook) open graph data should satisfy (minimum|full) requirements$/
```

Useful tips
------------
* Use [Symfony KernelDriver](https://github.com/Behat/Symfony2Extension) for improving the performance if you are working in a Symfony project.
