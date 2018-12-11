<?php

namespace MOrtola\BehatSEOContexts;

use HtmlValidator\Validator;

class HTMLContext extends BaseContext
{
    /**
     * @throws \Exception
     *
     * @Then the page HTML markup should be valid
     */
    public function thePageHtmlMarkupShouldBeValid()
    {
        $pageHtmlContent = $this->getSession()->getPage()->getContent();

        $validatorServices = [
            Validator::DEFAULT_VALIDATOR_URL,
            'https://validator.nu/',
            'https://validator.w3.org/nu/',
        ];

        foreach ($validatorServices as $validatorService) {
            try {
                $validator = new Validator($validatorService);
                $validatorResult = $validator->validateDocument($pageHtmlContent);
                break;
            } catch (\Exception $e) {
            }
        }

        if (!isset($validatorResult)) {
            throw new \Exception('HTML validation services are not working.');
        }

        $htmlErrors = $validatorResult->getErrors();

        if (isset($htmlErrors[0])) {
            throw new \Exception(
                sprintf(
                    'HTML markup validation error: Line %s: "%s" - %s in %s',
                    $htmlErrors[0]->getFirstLine(),
                    $htmlErrors[0]->getExtract(),
                    $htmlErrors[0]->getText(),
                    $this->getCurrentUrl()
                )
            );
        }
    }
}
