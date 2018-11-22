<?php

namespace MOrtola\BehatSEOContexts;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\Client;

class RedirectContext extends BaseContext
{
    /**
     * @throws UnsupportedDriverActionException
     *
     * @AfterScenario
     */
    public function enableFollowRedirects()
    {
        if ($this->getSession()->getDriver() instanceof BrowserKitDriver) {
            $this->iFollowRedirects();
        }
    }

    /**
     * @throws UnsupportedDriverActionException
     *
     * @Given /^I follow redirects$/
     */
    public function iFollowRedirects()
    {
        $this->getClient()->followRedirects(true);
    }

    /**
     * @return Client
     * @throws UnsupportedDriverActionException
     */
    private function getClient()
    {
        $this->supportsBrowserKitDriver();

        return $this->getSession()->getDriver()->getClient();
    }

    /**
     * @throws UnsupportedDriverActionException
     *
     * @Given /^I do not follow redirects$/
     */
    public function iDoNotFollowRedirects()
    {
        $this->getClient()->followRedirects(false);
    }

    /**
     * @param string $page
     *
     * @throws \Exception
     * @throws UnsupportedDriverActionException
     *
     * @Then /^I (?:am|should be) redirected(?: to "([^"]*)")?$/
     */
    public function iAmRedirected($page)
    {
        $headers = $this->getSession()->getResponseHeaders();

        if (empty($headers['Location']) && empty($headers['location'])) {
            throw new \Exception('The response should contain a "Location" header');
        }

        $headerLocation = empty($headers['Location']) ? $headers['location'] : $headers['Location'];

        if (is_array($headerLocation)) {
            $headerLocation = current($headerLocation);
        }

        Assert::assertTrue(
            $headerLocation === $page || $this->locatePath($page) === $this->locatePath($headerLocation),
            'The "Location" header does not redirect to the correct URI'
        );

        $client = $this->getClient();
        $client->followRedirects(true);
        $client->followRedirect();
    }
}
