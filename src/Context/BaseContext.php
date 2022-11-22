<?php

declare(strict_types=1);

namespace MarcOrtola\BehatSEOContexts\Context;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Driver\KernelDriver;
use InvalidArgumentException;
use MarcOrtola\BehatSEOContexts\Exception\TimeoutException;

class BaseContext extends RawMinkContext
{
    /**
     * @var string
     */
    protected $webUrl;

    /**
     * @BeforeScenario
     */
    public function setupWebUrl(): void
    {
        $this->webUrl = $this->getMinkParameter('base_url');
    }

    protected function getOuterHtml(NodeElement $nodeElement): string
    {
        if (method_exists($nodeElement, 'getOuterHtml')) {
            return $nodeElement->getOuterHtml();
        }

        return $nodeElement->getHtml();
    }

    protected function getResponseHeader(string $header): ?string
    {
        if (method_exists($this->getSession(), 'getResponseHeader')) {
            return $this->getSession()->getResponseHeader($header);
        }

        if (isset($this->getSession()->getResponseHeaders()[$header][0])) {
            return $this->getSession()->getResponseHeaders()[$header][0];
        }

        return null;
    }

    /**
     * @throws DriverException
     */
    protected function visit(string $url): void
    {
        $driver = $this->getSession()->getDriver();

        if ($driver instanceof KernelDriver) {
            $driver->getClient()->request('GET', $url);

            return;
        }

        $driver->visit($url);
    }

    protected function getStatusCode(): int
    {
        return $this->getSession()->getStatusCode();
    }

    /**
     * @throws TimeoutException
     */
    protected function spin(callable $closure, int $seconds = 5): bool
    {
        $iteration = 1;
        while ($iteration++ <= $seconds * 4) {
            if ($closure($this)) {
                return true;
            }
            $this->getSession()->wait(1000 / 4);
        }
        $backtrace = debug_backtrace();

        throw new TimeoutException(
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

        return $url;
    }

    protected function getCurrentUrl(): string
    {
        return $this->getSession()->getCurrentUrl();
    }

    /**
     * @throws UnsupportedDriverActionException
     */
    protected function supportsDriver(string $driverClass): void
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
    protected function doesNotSupportDriver(string $driverClass): void
    {
        if (is_a($this->getSession()->getDriver(), $driverClass)) {
            throw new UnsupportedDriverActionException(
                sprintf('This step is not supported by the %s driver', $driverClass),
                $this->getSession()->getDriver()
            );
        }
    }

    protected function assertInverse(callable $callableStepDefinition, string $exceptionMessage = ''): void
    {
        try {
            $callableStepDefinition();
        } catch (InvalidArgumentException $e) {
            return;
        }

        throw new InvalidArgumentException($exceptionMessage);
    }
}
