<?php declare(strict_types=1);

namespace MOrtola\BehatSEOContexts\Context;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;

class SocialContext extends BaseContext
{
    /**
     * @Then /^the (Twitter|Facebook) Open Graph data should not satisfy (minimum|full) requirements$/
     */
    public function theOpenGraphDataShouldNotSatisfyRequirements(
        string $socialNetworkName,
        string $requirementsType
    ): void {
        $this->assertInverse(
            function () use ($socialNetworkName, $requirementsType) {
                $this->theOpenGraphDataShouldSatisfyRequirements($socialNetworkName, $requirementsType);
            },
            sprintf('The %s OG Data satisfies %s requirements.', $socialNetworkName, $requirementsType)
        );
    }

    /**
     * @throws Exception
     *
     * @Then /^the (Twitter|Facebook) Open Graph data should satisfy (minimum|full) requirements$/
     */
    public function theOpenGraphDataShouldSatisfyRequirements(string $socialNetworkName, string $requirementsType): void
    {
        if ('full' === $requirementsType) {
            switch ($socialNetworkName) {
                case 'Twitter':
                    $this->validateFullTwitterOpenGraphData();

                    break;
                case 'Facebook':
                    $this->validateFullFacebookOpenGraphData();

                    break;
                default:
                    throw new InvalidArgumentException(
                        sprintf('%s open graph full validation is not allowed.', $socialNetworkName)
                    );
            }

            return;
        }

        switch ($socialNetworkName) {
            case 'Twitter':
                $this->validateTwitterOpenGraphData();

                break;
            case 'Facebook':
                $this->validateFacebookOpenGraphData();

                break;
            default:
                throw new InvalidArgumentException(
                    sprintf('%s open graph simple validation is not allowed.', $socialNetworkName)
                );
        }
    }

    /**
     * @throws Exception
     */
    private function validateTwitterOpenGraphData(): void
    {
        Assert::assertContains(
            $this->getOGMetaContent('twitter:card'),
            ['summary', 'summary_large_image', 'app', 'player'],
            'OG meta twitter:card contains invalid content'
        );

        $this->getOGMetaContent('twitter:title');
    }

    /**
     * @throws Exception
     */
    private function validateFullTwitterOpenGraphData(): void
    {
        $this->validateTwitterOpenGraphData();

        Assert::assertNotFalse(
            filter_var($this->getOGMetaContent('twitter:image'), FILTER_VALIDATE_URL)
        );

        $pathInfo = pathinfo($this->getOGMetaContent('twitter:image'));

        if (isset($pathInfo['extension'])) {
            Assert::assertContains(
                $pathInfo['extension'],
                ['jpg', 'jpeg', 'webp', 'png', 'gif'],
                'OG meta twitter:image has valid extension. Allowed are: jpg/jpeg, png, webp, gif'
            );
        }

        $this->getOGMetaContent('twitter:description');
    }

    /**
     * @throws Exception
     */
    private function getOGMetaContent(string $property): string
    {
        $ogMeta = $this->getSession()->getPage()->find(
            'xpath',
            sprintf('//head/meta[@property="%1$s" or @name="%1$s"]', $property)
        );

        Assert::assertNotNull(
            $ogMeta,
            sprintf('Open Graph meta %s does not exist', $property)
        );

        if ($ogMeta) {
            Assert::assertNotEmpty(
                $ogMeta->getAttribute('content'),
                sprintf('Open Graph meta %s should not be empty', $property)
            );

            return $ogMeta->getAttribute('content') ?? '';
        }

        return '';
    }

    /**
     * @throws Exception
     */
    private function validateFacebookOpenGraphData(): void
    {
        Assert::assertNotFalse(
            filter_var($this->getOGMetaContent('og:url'), FILTER_VALIDATE_URL)
        );

        Assert::assertEquals(
            $this->getOGMetaContent('og:url'),
            $this->getCurrentUrl(),
            'OG meta og:url does not match expected url'
        );

        $this->getOGMetaContent('og:title');
        $this->getOGMetaContent('og:description');

        Assert::assertNotFalse(
            filter_var($this->getOGMetaContent('og:image'), FILTER_VALIDATE_URL)
        );

        $pathInfo = pathinfo($this->getOGMetaContent('og:image'));

        if (isset($pathInfo['extension'])) {
            Assert::assertContains(
                $pathInfo['extension'],
                ['jpg', 'jpeg', 'png', 'gif'],
                'OG meta og:image has valid extension. Allowed are: jpg/jpeg, png, gif'
            );
        }
    }

    /**
     * @throws Exception
     */
    private function validateFullFacebookOpenGraphData(): void
    {
        $this->validateFacebookOpenGraphData();

        Assert::assertContains(
            $this->getOGMetaContent('og:type'),
            [
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
            ],
            'OG meta og:type contains invalid content.'
        );

        Assert::assertRegExp(
            '/^[a-z]{2}_[A-Z]{2}$/',
            $this->getOGMetaContent('og:locale'),
            'OG meta og:locale does not follow the right format az_AZ.'
        );
    }
}
