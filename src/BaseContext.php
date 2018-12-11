<?php

namespace MOrtola\BehatSEOContexts;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Driver\KernelDriver;

class BaseContext extends RawMinkContext
{
    /**
     * @var string
     */
    protected $webUrl;

    /**
     * @BeforeScenario
     */
    public function setupWebUrl()
    {
        $this->webUrl = $this->getMinkParameter('base_url');
    }

    /**
     * @param string $url
     *
     * @throws DriverException
     */
    protected function visit($url)
    {
        $driver = $this->getSession()->getDriver();

        if ($driver instanceof KernelDriver) {
            $driver->getClient()->request('GET', $url);
        } else {
            $driver->visit($url);
        }
    }

    /**
     * @return int
     */
    protected function getStatusCode()
    {
        return $this->getSession()->getStatusCode();
    }

    /**
     * @param     $closure
     * @param int $seconds
     *
     * @return bool
     * @throws \Exception
     */
    protected function spin(callable $closure, $seconds = 5)
    {
        $fraction = 4;
        $max = $seconds * $fraction;
        $i = 1;
        while ($i++ <= $max) {
            if ($closure($this)) {
                return true;
            }
            $this->getSession()->wait(1000 / $fraction);
        }
        $backtrace = debug_backtrace();

        throw new \Exception(
            sprintf(
                "Timeout thrown by %s::%s()\n%s, line %s",
                $backtrace[0]['class'],
                $backtrace[0]['function'],
                $backtrace[0]['file'],
                $backtrace[0]['line']
            )
        );
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function toAbsoluteUrl($url)
    {
        if (false === strpos($url, '://')) {
            $url = sprintf('%s%s', $this->webUrl, $url);
        }

        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(
                sprintf('%s is not a valid URL', $url)
            );
        }

        return $url;
    }

    /**
     * @param bool $relative
     *
     * @return string
     */
    protected function getCurrentUrl($relative = false)
    {
        $url = $this->getSession()->getCurrentUrl();

        return $relative ? $this->toRelativeUrl($url) : $url;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function toRelativeUrl($url)
    {
        return parse_url($url, PHP_URL_PATH);
    }

    /**
     * @param bool $supported
     *
     * @throws UnsupportedDriverActionException
     */
    protected function supportsJavascript($supported = true)
    {
        $this->supportsDrivers([Selenium2Driver::class, 'DMore\ChromeDriver\ChromeDriver'], $supported);
    }

    /**
     * @param array $driverClasses
     * @param bool $supported
     *
     * @throws UnsupportedDriverActionException
     */
    private function supportsDrivers($driverClasses, $supported)
    {
        $driver = $this->getSession()->getDriver();

        $isSearchedDriver = false;
        foreach ($driverClasses as $driverClass) {
            if (is_a($driver, $driverClass)) {
                $isSearchedDriver = true;
            }
        }

        if ($supported && !$isSearchedDriver) {
            throw new UnsupportedDriverActionException(
                sprintf('This step is only supported by the %s driver', implode(',', $driverClasses)),
                $driver
            );
        } elseif (!$supported && $isSearchedDriver) {
            throw new UnsupportedDriverActionException(
                sprintf('This step is not supported by the %s driver', implode(',', $driverClasses)),
                $driver
            );
        }
    }

    /**
     * @param bool $supported
     *
     * @throws UnsupportedDriverActionException
     */
    protected function supportsSymfony($supported = true)
    {
        $this->supportsDrivers([KernelDriver::class], $supported);
    }

    /**
     * @param bool $supported
     *
     * @throws UnsupportedDriverActionException
     */
    protected function supportsBrowserKitDriver($supported = true)
    {
        $this->supportsDrivers([BrowserKitDriver::class], $supported);
    }
}
