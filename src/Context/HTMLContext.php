<?php declare(strict_types=1);

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Exception;
use HtmlValidator\Exception\ServerException;
use HtmlValidator\Validator;
use PHPUnit\Framework\ExpectationFailedException;

class HTMLContext extends BaseContext
{
    const VALIDATION_SERVICES = [
        Validator::DEFAULT_VALIDATOR_URL,
        'https://validator.nu/',
        'https://validator.w3.org/nu/',
    ];

    /**
     * @throws Exception
     *
     * @Then the page HTML markup should be valid
     */
    public function thePageHtmlMarkupShouldBeValid(): void
    {
        foreach (self::VALIDATION_SERVICES as $validatorService) {
            try {
                $validator       = new Validator($validatorService);
                $validatorResult = $validator->validateDocument($this->getSession()->getPage()->getContent());
                break;
            } catch (ServerException $e) {
                // @ignoreException
            }
        }

        if (!isset($validatorResult)) {
            throw new PendingException('HTML validation services are not working');
        } elseif (isset($validatorResult->getErrors()[0])) {
            throw new ExpectationFailedException(
                sprintf(
                    'HTML markup validation error: Line %s: "%s" - %s in %s',
                    $validatorResult->getErrors()[0]->getFirstLine(),
                    $validatorResult->getErrors()[0]->getExtract(),
                    $validatorResult->getErrors()[0]->getText(),
                    $this->getCurrentUrl()
                )
            );
        }
    }

    /**
     * @throws Exception
     *
     * @Then the page HTML markup should not be valid
     */
    public function thePageHtmlMarkupShouldNotBeValid(): void
    {
        $this->assertInverse(
            [$this, 'thePageHtmlMarkupShouldBeValid'],
            'HTML markup should not be valid.'
        );
    }
}
