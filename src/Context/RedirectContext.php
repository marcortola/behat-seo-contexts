<?php

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\Client;

class RedirectContext extends BaseContext
{
    /**
     * @AfterScenario
     */
    public function enableFollowRedirects()
    {
        try {
            $this->iFollowRedirects();
        } catch (UnsupportedDriverActionException $e) {
            return;
        }
    }

    /**
     * @throws UnsupportedDriverActionException
     *
     * @Given I follow redirects
     */
    public function iFollowRedirects()
    {
        $this->getClient()->followRedirects(true);
    }

    /**
     * @throws UnsupportedDriverActionException
     */
    private function getClient(): Client
    {
        $this->supportsDriver(BrowserKitDriver::class);

        return $this->getSession()->getDriver()->getClient();
    }

    /**
     * @throws UnsupportedDriverActionException
     *
     * @Given I do not follow redirects
     */
    public function iDoNotFollowRedirects()
    {
        $this->getClient()->followRedirects(false);
    }

    /**
     * @throws \Exception
     * @throws UnsupportedDriverActionException
     *
     * @Then I should be redirected to :url
     */
    public function iShouldBeRedirected(string $url)
    {
        $headers = array_change_key_case($this->getSession()->getResponseHeaders(), CASE_LOWER);

        if (empty($headers['location'])) {
            throw new \Exception('The response should contain a "Location" header');
        }

        if (isset($headers['location'][0])) {
            $headers['location'] = $headers['location'][0];
        }

        Assert::assertTrue(
            $headers['location'] === $url || $this->locatePath($url) === $this->locatePath($headers['location']),
            'The "Location" header does not redirect to the correct URI'
        );

        $this->getClient()->followRedirects(true);
        $this->getClient()->followRedirect();
    }
}
