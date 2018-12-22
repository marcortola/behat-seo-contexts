<?php

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PHPUnit\Framework\Assert;

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
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $this->robotsContext = $scope->getEnvironment()->getContext(RobotsContext::class);
        $this->metaContext = $scope->getEnvironment()->getContext(MetaContext::class);
    }

    /**
     * @Then the page should not be indexable
     */
    public function thePageShouldNotBeIndexable()
    {
        $this->assertInverse(
            [$this, 'thePageShouldBeIndexable'],
            'The page is indexable.'
        );
    }

    /**
     * @throws \Exception
     *
     * @Then the page should be indexable
     */
    public function thePageShouldBeIndexable()
    {
        $this->metaContext->thePageShouldNotBeNoindex();
        $this->robotsContext->iShouldBeAbleToCrawl($this->getCurrentUrl());

        $robotsHeaderTag = $this->getSession()->getResponseHeader('X-Robots-Tag');

        if ($robotsHeaderTag) {
            Assert::assertNotContains(
                'noindex',
                strtolower($robotsHeaderTag),
                sprintf(
                    'Url %s should not send X-Robots-Tag HTTP header with noindex value: %s',
                    $this->getCurrentUrl(),
                    $robotsHeaderTag
                )
            );
        }
    }
}
