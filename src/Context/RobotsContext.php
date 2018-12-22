<?php

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Symfony2Extension\Driver\KernelDriver;
use PHPUnit\Framework\Assert;
use vipnytt\RobotsTxtParser\UriClient;

class RobotsContext extends BaseContext
{
    /**
     * @var string
     */
    private $crawlerUserAgent = 'Googlebot';

    /**
     * @Given I am a :crawlerUserAgent crawler
     */
    public function iAmACrawler(string $crawlerUserAgent)
    {
        $this->crawlerUserAgent = $crawlerUserAgent;
    }

    /**
     * @throws \Exception
     *
     * @Then I should not be able to crawl :resource
     */
    public function iShouldNotBeAbleToCrawl(string $resource)
    {
        Assert::assertFalse(
            $this->getRobotsClient()->userAgent($this->crawlerUserAgent)->isAllowed($resource),
            sprintf(
                'Crawler with User-Agent %s is allowed to crawl %s',
                $this->crawlerUserAgent,
                $resource
            )
        );
    }

    private function getRobotsClient(): UriClient
    {
        return new UriClient($this->webUrl);
    }

    /**
     * @throws \Exception
     * @throws UnsupportedDriverActionException
     *
     * @Then I should be able to get the sitemap URL
     */
    public function iShouldBeAbleToGetTheSitemapUrl()
    {
        $this->doesNotSupportDriver(KernelDriver::class);

        $sitemaps = $this->getRobotsClient()->sitemap()->export();

        Assert::assertFalse(
            empty($sitemaps),
            sprintf('Crawler with User-Agent %s can not find a sitemap url in robots file.', $this->crawlerUserAgent)
        );

        Assert::assertEquals(
            1,
            count($sitemaps),
            sprintf(
                'Crawler with User-Agent %s has find more than 1 sitemap url in robots file.',
                $this->crawlerUserAgent
            )
        );

        try {
            $this->getSession()->visit($sitemaps[0]);
        } catch (\Exception $e) {
            throw new \Exception(
                sprintf(
                    'Sitemap url %s is not valid. Exception: %s',
                    $sitemaps[0],
                    $e->getMessage()
                )
            );
        }

        Assert::assertEquals(
            200,
            $this->getStatusCode(),
            sprintf('Sitemap url %s is not valid.', $sitemaps[0])
        );
    }

    /**
     * @throws \Exception
     *
     * @Then I should be able to crawl :resource
     */
    public function iShouldBeAbleToCrawl(string $resource)
    {
        Assert::assertTrue(
            $this->getRobotsClient()->userAgent($this->crawlerUserAgent)->isAllowed($resource),
            sprintf(
                'Crawler with User-Agent %s is not allowed to crawl %s',
                $this->crawlerUserAgent,
                $resource
            )
        );
    }
}
