# Behat Website Contexts
Behat contexts for testing common website frontend and SEO related aspects. Includes contexts for testing: SEO, Performance and Assets optimization, Redirects, Robots, Sitemap and Social Media Optimization.

Installation
------------

Basic requirements:

* Php 5.6+
* Behat 3+
* Mink + Mink extension
* Phpunit

### How to install it

1. [Install Composer](https://getcomposer.org/download/)
2. Execute:

```
$ composer require mortola/behat-website-contexts --dev
```

3. Add the Context you need to `behat.yml`:

```yaml
# behat.yml
default:
    # ...
    suites:
        default:
          contexts:
            - MOrtola\BehatWebsiteContexts\CommandContext
            - MOrtola\BehatWebsiteContexts\SitemapContext
            - MOrtola\BehatWebsiteContexts\RedirectContext
            - MOrtola\BehatWebsiteContexts\RobotsContext
            - MOrtola\BehatWebsiteContexts\PerformanceContext
            - MOrtola\BehatWebsiteContexts\SocialContext
            - MOrtola\BehatWebsiteContexts\SEOContext
```
### Most used steps
##### CommandContext
```
Given I run command :command
Then I should see :string in the command output
Then I should not see :notExpectedOutput in the command output
```
##### SitemapContext
```
Given /^the (index|multilanguage|) sitemap "(.*)"$/
Then the index sitemap should have child :childSitemapUrl
Then /^the sitemap has ([0-9]+) children$/
Then the multilanguage sitemap pass Google validation
Then the sitemap urls are alive
```
##### RedirectContext
```
Given /^I follow redirects$/
Given /^I do not follow redirects$/
Then /^I (?:am|should be) redirected(?: to "([^"]*)")?$/
```
##### RobotsContext
```
Given I am a :crawlerUserAgent crawler
Then I should not be able to crawl :resource
Then I should be able to crawl :resource
Then I should be able to get the sitemap url
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
Then I should see :text in the facebook comment plugin
Then I should see a facebook comment plugin
Then /^the (twitter|facebook) open graph data should satisfy (minimum|full) requirements$/
```
##### SEOContext
```
Then the page should not be noindex
Then the page canonical should be :expectedCanonicalUrl
Then the page hreflang markup should be valid
Then the page HTML markup should be valid
Then the page meta title should be :expectedMetaTitle
Then the page meta description should be :expectedMetaDescription
```

Useful tips
------------
* Use [Symfony KernelDriver](https://github.com/Behat/Symfony2Extension) for improving the performance if you are working in a Symfony project.
