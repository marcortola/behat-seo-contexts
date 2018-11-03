<?php

namespace MOrtola\BehatWebsiteContexts;

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

class CommandContext implements Context
{
    /**
     * @var string
     */
    private $output;

    /**
     * @var string
     */
    private $executedCommand;

    /**
     * @param string $command
     *
     * @Given I run command :command
     */
    public function iRunCommand($command)
    {
        $this->executedCommand = $command;
        $this->output = shell_exec($command);

        if (null == $this->output) {
            $this->output = '';
        }

        $this->output = trim($this->output);
    }

    /**
     * @param string $expectedOutput
     * @throws \Exception
     *
     * @Then I should see :string in the command output
     */
    public function iShouldSeeInTheCommandOutput($expectedOutput)
    {
        $this->assertCommandHasBeenExecuted();

        Assert::assertContains(
            $expectedOutput,
            $this->output,
            sprintf('Did not see "%s" in output "%s" when running "%s" command', $expectedOutput, $this->output, $this->executedCommand)
        );
    }

    /**
     * @throws \Exception
     */
    private function assertCommandHasBeenExecuted()
    {
        if (null == $this->executedCommand) {
            throw new \Exception(
                'You should execute "@Given I run command :command" step before executing this step.'
            );
        }
    }

    /**
     * @param string $notExpectedOutput
     * @throws \Exception
     *
     * @Then I should not see :notExpectedOutput in the command output
     */
    public function iShouldNotSeeInTheCommandOutput($notExpectedOutput)
    {
        $this->assertCommandHasBeenExecuted();

        Assert::assertNotContains(
            $notExpectedOutput,
            $this->output,
            sprintf('Did see "%s" in output "%s" when running "%s" command', $notExpectedOutput, $this->output, $this->executedCommand)
        );
    }
}
