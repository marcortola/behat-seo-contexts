<?php

namespace MOrtola\BehatSEOContexts\Context;

use PHPUnit\Framework\Assert;

class SocialContext extends BaseContext
{
    /**
     * @Then /^the (twitter|facebook) open graph data should not satisfy (minimum|full) requirements$/
     */
    public function theOpenGraphDataShouldNotSatisfyRequirements(string $socialNetworkName, string $requirementsType)
    {
        $this->assertInverse(
            function () use ($socialNetworkName, $requirementsType) {
                $this->theOpenGraphDataShouldSatisfyRequirements($socialNetworkName, $requirementsType);
            },
            sprintf('The %s OG Data satisfies %s requirements.', $socialNetworkName, $requirementsType)
        );
    }

    /**
     * @throws \Exception
     *
     * @Then /^the (twitter|facebook) open graph data should satisfy (minimum|full) requirements$/
     */
    public function theOpenGraphDataShouldSatisfyRequirements(string $socialNetworkName, string $requirementsType)
    {
        $fullRequirements = 'full' === $requirementsType ? true : false;

        switch ($socialNetworkName) {
            case 'twitter':
                $this->validateTwitterOpenGraphData($fullRequirements);

                break;
            case 'facebook':
                $this->validateFacebookOpenGraphData($fullRequirements);

                break;
            default:
                throw new \Exception(
                    sprintf('%s open graph validation is not allowed.', $socialNetworkName)
                );
        }
    }

    /**
     * @throws \Exception
     */
    private function validateTwitterOpenGraphData(bool $fullRequirements = false)
    {
        /* OG meta twitter:card */
        $twitterCard = $this->getOGMetaContent('twitter:card');

        Assert::assertContains(
            $twitterCard,
            ['summary', 'summary_large_image', 'app', 'player'],
            sprintf('OG meta twitter:card contains invalid content "%s"', $twitterCard)
        );

        /* OG meta twitter:title */
        $this->getOGMetaContent('twitter:title');

        if ($fullRequirements) {
            /* OG meta twitter:image (optional) */
            $twitterImage = $this->getOGMetaContent('twitter:image');

            Assert::assertNotFalse(
                filter_var($twitterImage, FILTER_VALIDATE_URL)
            );

            $twitterImageExtension = pathinfo($twitterImage)['extension'];

            Assert::assertContains(
                $twitterImageExtension,
                ['jpg', 'jpeg', 'webp', 'png', 'gif'],
                sprintf(
                    'OG meta twitter:image has valid extension %s. Allowed are: jpg/jpeg, png, webp, gif',
                    $twitterImageExtension
                )
            );

            /* OG meta twitter:description (optional) */
            $this->getOGMetaContent('twitter:description');
        }
    }

    /**
     * @throws \Exception
     */
    private function getOGMetaContent(string $property): string
    {
        $xpath = sprintf('//head/meta[@property="%1$s" or @name="%1$s"]', $property);
        $meta = $this->getSession()->getPage()->find('xpath', $xpath);

        Assert::assertNotNull(
            $meta,
            sprintf('Open Graph meta %s does not exist', $property)
        );

        $metaContent = $meta->getAttribute('content');

        Assert::assertNotEmpty(
            $metaContent,
            sprintf('Open Graph meta %s should not be empty', $property)
        );

        return $metaContent;
    }

    /**
     * @throws \Exception
     */
    private function validateFacebookOpenGraphData(bool $fullRequirements = false)
    {
        /* OG meta og:url */
        $facebookUrl = $this->getOGMetaContent('og:url');

        Assert::assertNotFalse(
            filter_var($facebookUrl, FILTER_VALIDATE_URL)
        );

        Assert::assertEquals(
            $facebookUrl,
            $this->getCurrentUrl(),
            'OG meta og:url does not match expected url'
        );

        /* OG meta og:title */
        $this->getOGMetaContent('og:title');

        /* OG meta og:description */
        $this->getOGMetaContent('og:description');

        /* OG meta og:image */
        $facebookImage = $this->getOGMetaContent('og:image');

        Assert::assertNotFalse(
            filter_var($facebookImage, FILTER_VALIDATE_URL)
        );

        $facebookImageExtension = pathinfo($facebookImage)['extension'];

        Assert::assertContains(
            $facebookImageExtension,
            ['jpg', 'jpeg', 'png', 'gif'],
            sprintf(
                'OG meta og:image has valid extension %s. Allowed are: jpg/jpeg, png, gif',
                $facebookImageExtension
            )
        );

        if ($fullRequirements) {
            /* OG meta og:type (optional) */
            $facebookType = $this->getOGMetaContent('og:type');

            $allowedFacebookTypes = [
                'article',
                'book',
                'books.author',
                'books.book',
                'books.genre',
                'business.business',
                'fitness.course',
                'game.achievement',
                'music.album',
                'music.playlist',
                'music.radio_station',
                'music.song',
                'place',
                'product',
                'product.group',
                'product.item',
                'profile',
                'restaurant.menu',
                'restaurant.menu_item',
                'restaurant.menu_section',
                'restaurant.restaurant',
                'video.episode',
                'video.movie',
                'video.other',
                'video.tv_show',
            ];

            Assert::assertContains(
                $facebookType,
                $allowedFacebookTypes,
                sprintf(
                    'OG meta og:type contains invalid content "%s". Allowed: %s',
                    $facebookType,
                    implode(',', $allowedFacebookTypes)
                )
            );

            /* OG meta og:locale (optional) */
            $facebookLocale = $this->getOGMetaContent('og:locale');

            Assert::assertRegExp(
                '/^[a-z]{2}_[A-Z]{2}$/',
                $facebookLocale,
                sprintf('OG meta og:locale does not follow the right format az_AZ. Actual: %s', $facebookLocale)
            );
        }
    }
}
