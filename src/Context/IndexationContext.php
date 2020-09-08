<?php

declare(strict_types=1);

namespace MarcOrtola\BehatSEOContexts\Context;

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Webmozart\Assert\Assert;

class IndexationContext extends BaseContext
{
    /**
     * @var RobotsContext
     */
    private $robotsContext;

    /**
     * @var MetaContext
     */
    private $metaContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $env = $scope->getEnvironment();

        if ($env instanceof InitializedContextEnvironment) {
            $this->robotsContext = $env->getContext(RobotsContext::class);
            $this->metaContext = $env->getContext(MetaContext::class);
        }
    }

    /**
     * @Then the page should not be indexable
     */
    public function thePageShouldNotBeIndexable(): void
    {
        $this->assertInverse(
            [$this, 'thePageShouldBeIndexable'],
            'The page is indexable.'
        );
    }

    /**
     * @Then the page should be indexable
     */
    public function thePageShouldBeIndexable(): void
    {
        $this->metaContext->thePageShouldNotBeNoindex();
        $this->robotsContext->iShouldBeAbleToCrawl($this->getCurrentUrl());

        if ($robotsHeaderTag = $this->getResponseHeader('X-Robots-Tag')) {
            Assert::notContains(
                strtolower($robotsHeaderTag),
                'noindex',
                sprintf(
                    'Url %s should not send X-Robots-Tag HTTP header with noindex value: %s',
                    $this->getCurrentUrl(),
                    $robotsHeaderTag
                )
            );
        }
    }
}
