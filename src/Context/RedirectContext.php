<?php

declare(strict_types=1);

namespace MarcOrtola\BehatSEOContexts\Context;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use InvalidArgumentException;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Webmozart\Assert\Assert;

class RedirectContext extends BaseContext
{
    /**
     * @AfterScenario
     */
    public function enableFollowRedirects(): void
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
    public function iFollowRedirects(): void
    {
        $this->getClient()->followRedirects(true);
    }

    /**
     * @throws UnsupportedDriverActionException
     */
    private function getClient(): AbstractBrowser
    {
        $this->supportsDriver(BrowserKitDriver::class);

        if (method_exists($this->getSession()->getDriver(), 'getClient')) {
            return $this->getSession()->getDriver()->getClient();
        }

        throw new InvalidArgumentException();
    }

    /**
     * @throws UnsupportedDriverActionException
     *
     * @Given I do not follow redirects
     */
    public function iDoNotFollowRedirects(): void
    {
        $this->getClient()->followRedirects(false);
    }

    /**
     * @throws UnsupportedDriverActionException
     *
     * @Then I should be redirected to :url
     */
    public function iShouldBeRedirected(string $url): void
    {
        $headers = array_change_key_case($this->getSession()->getResponseHeaders(), CASE_LOWER);

        Assert::keyExists($headers, 'location');

        if (isset($headers['location'][0])) {
            $headers['location'] = $headers['location'][0];
        }

        Assert::true(
            $headers['location'] === $url || $this->locatePath($url) === $this->locatePath($headers['location']),
            'The "Location" header does not redirect to the correct URI'
        );

        $this->getClient()->followRedirects(true);
        $this->getClient()->followRedirect();
    }
}
