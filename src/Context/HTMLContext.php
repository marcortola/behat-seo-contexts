<?php

namespace MOrtola\BehatSEOContexts\Context;

use HtmlValidator\Response;
use HtmlValidator\Validator;
use PHPUnit\Framework\ExpectationFailedException;

class HTMLContext extends BaseContext
{
    const VALIDATION_SERVICES = [
        Validator::DEFAULT_VALIDATOR_URL,
        'https://validator.nu/',
        'https://validator.w3.org/nu/'
    ];

    /**
     * @throws \Exception
     *
     * @Then the page HTML markup should be valid
     */
    public function thePageHtmlMarkupShouldBeValid()
    {
        foreach (self::VALIDATION_SERVICES as $validatorService) {
            try {
                $validator = new Validator($validatorService);
                /** @var Response $validatorResult */
                $validatorResult = $validator->validateDocument($this->getSession()->getPage()->getContent());
                break;
            } catch (\Exception $e) {
            }
        }

        if (!isset($validatorResult)) {
            throw new \Exception('HTML validation services are not working.');
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
     * @throws \Exception
     *
     * @Then the page HTML markup should not be valid
     */
    public function thePageHtmlMarkupShouldNotBeValid()
    {
        $this->assertInverse(
            [$this, 'thePageHtmlMarkupShouldBeValid'],
            'HTML markup should not be valid.'
        );
    }
}
