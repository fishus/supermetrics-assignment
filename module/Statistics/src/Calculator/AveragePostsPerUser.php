<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class AveragePostsPerUser extends AbstractCalculator
{
    protected const UNITS = 'posts';

    /**
     * @var int Number of posts by all users
     */
    private int $totalPosts = 0;

    /**
     * @var int Total users
     */
    private int $usersCount = 0;

    /**
     * @var array Users list ids
     */
    private array $usersIds = [];

    /**
     * @var array Users posts count (For another solution)
     */
    private array $usersPosts = [];

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        // First solution: Immediately count the number of users and posts
        $this->totalPosts++;

        $authorId = $postTo->getAuthorId();

        if (!array_key_exists($authorId, $this->usersIds)) {
            $this->usersIds[$authorId] = $postTo->getAuthorId();
            $this->usersCount++;
        }

        // Another solution is sum all posts in users list
        // Collect info about users and posts, but calculate the total number of posts and users later
        if (!array_key_exists($authorId, $this->usersPosts)) {
            $this->usersPosts[$authorId] = 1;
        }
        else {
            $this->usersPosts[$authorId]++;
        }
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        $value = $this->usersCount > 0
            ? $this->totalPosts / $this->usersCount
            : 0;

        // Another solution (worse) is sum all posts in users list
        $value2 = !empty($this->usersPosts)
            ? array_sum($this->usersPosts) / count($this->usersPosts)
            : 0;

        return (new StatisticsTo())->setValue(round($value, 2));
    }
}
