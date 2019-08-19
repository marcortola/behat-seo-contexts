<?php declare(strict_types=1);

namespace MOrtola\BehatSEOContexts\Context;

use Behat\Behat\Tester\Exception\PendingException;
use HtmlValidator\Exception\ServerException;
use HtmlValidator\Exception\UnknownParserException;
use HtmlValidator\Validator;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

class HTMLContext extends BaseContext
{
    const VALIDATION_SERVICES = [
        Validator::DEFAULT_VALIDATOR_URL,
        'https://validator.nu/',
        'https://validator.w3.org/nu/',
    ];

    /**
     * @Then the page HTML markup should be valid
     */
    public function thePageHtmlMarkupShouldBeValid(): void
    {
        $validated        = false;
        $validationErrors = [];

        foreach (self::VALIDATION_SERVICES as $validatorService) {
            try {
                $validator        = new Validator($validatorService);
                $validatorResult  = $validator->validateDocument($this->getSession()->getPage()->getContent());
                $validated        = true;
                $validationErrors = $validatorResult->getErrors();
                break;
            } catch (ServerException | UnknownParserException $e) {
                // @ignoreException
            }
        }

        if (!$validated) {
            throw new PendingException('HTML validation services are not working');
        }

        if (isset($validationErrors[0])) {
            throw new InvalidArgumentException(
                sprintf(
                    'HTML markup validation error: Line %s: "%s" - %s in %s',
                    $validationErrors[0]->getFirstLine(),
                    $validationErrors[0]->getExtract(),
                    $validationErrors[0]->getText(),
                    $this->getCurrentUrl()
                )
            );
        }
    }

    /**
     * @Then the page HTML markup should not be valid
     */
    public function thePageHtmlMarkupShouldNotBeValid(): void
    {
        $this->assertInverse(
            [$this, 'thePageHtmlMarkupShouldBeValid'],
            'HTML markup should not be valid.'
        );
    }

    /**
     * @Then the page HTML5 doctype declaration should be valid
     */
    public function thePageHtml5DoctypeDeclarationShouldBeValid(): void
    {
        $pageContent = preg_replace(
            '/<!--(.|\s)*?-->/',
            '',
            $this->getSession()->getPage()->getContent()
        ) ?? '';

        Assert::startsWith(
            strtolower(trim($pageContent)),
            '<!doctype html>'
        );
    }

    /**
     * @Then the page HTML5 doctype declaration should not be valid
     */
    public function thePageHtml5DoctypeDeclarationShouldNotBeValid(): void
    {
        $this->assertInverse(
            [$this, 'thePageHtml5DoctypeDeclarationShouldBeValid'],
            'The page HTML5 doctype declaration should not be valid.'
        );
    }
}
