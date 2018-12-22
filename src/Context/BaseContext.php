<?php

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Driver\KernelDriver;
use PHPUnit\Framework\ExpectationFailedException;

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
     * @throws DriverException
     */
    protected function visit(string $url)
    {
        $driver = $this->getSession()->getDriver();

        if ($driver instanceof KernelDriver) {
            $driver->getClient()->request('GET', $url);
        } else {
            $driver->visit($url);
        }
    }

    protected function getStatusCode(): int
    {
        return $this->getSession()->getStatusCode();
    }

    /**
     * @throws \Exception
     */
    protected function spin(callable $closure, int $seconds = 5): bool
    {
        $i = 1;
        while ($i++ <= $seconds * 4) {
            if ($closure($this)) {
                return true;
            }
            $this->getSession()->wait(1000 / 4);
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

    protected function toAbsoluteUrl(string $url): string
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

    protected function getCurrentUrl(bool $relative = false): string
    {
        if ($relative) {
            $this->toRelativeUrl($this->getSession()->getCurrentUrl());
        }

        return $this->getSession()->getCurrentUrl();
    }

    protected function toRelativeUrl(string $url): string
    {
        return parse_url($url, PHP_URL_PATH);
    }

    /**
     * @throws UnsupportedDriverActionException
     */
    protected function supportsDriver(string $driverClass)
    {
        if (!is_a($this->getSession()->getDriver(), $driverClass)) {
            throw new UnsupportedDriverActionException(
                sprintf('This step is only supported by the %s driver', $driverClass),
                $this->getSession()->getDriver()
            );
        }
    }

    /**
     * @throws UnsupportedDriverActionException
     */
    protected function doesNotSupportDriver(string $driverClass)
    {
        if (is_a($this->getSession()->getDriver(), $driverClass)) {
            throw new UnsupportedDriverActionException(
                sprintf('This step is not supported by the %s driver', $driverClass),
                $this->getSession()->getDriver()
            );
        }
    }

    protected function assertInverse(callable $callableStepDefinition, string $exceptionMessage = '')
    {
        try {
            $callableStepDefinition();
        } catch (ExpectationFailedException $e) {
            return;
        }

        throw new ExpectationFailedException($exceptionMessage);
    }
}
