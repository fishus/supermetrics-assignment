<?php declare(strict_types = 1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use SocialPost\Driver\FictionalDriver;
use SocialPost\Dto\SocialPostTo;
use SocialPost\Hydrator\FictionalPostHydrator;
use Statistics\Dto\ParamsTo;
use Statistics\Enum\StatsEnum;

/**
 * Register token test
 *
 * @package Tests\unit
 */
class CalculatorTest extends TestCase
{
    protected array $posts = [];

    protected \DateTime $date;

    protected function setUp(): void
    {
        if (!$this->posts) {
            $fakeFictionalDriver = $this->getFakeFictionalDriver();

            $hydrator = new FictionalPostHydrator();

            foreach ($fakeFictionalDriver->fetchPostsByPage(1) as $postData) {
                $this->posts[] = $hydrator->hydrate($postData);
            }
        }

        if (!isset($this->date)) {
            $this->date = \DateTime::createFromFormat('Y-m', '2018-08');
        }
    }

    /**
     * @test
     */
    public function testCalculateAveragePostLength(): void
    {

        $startDate = $this->getStartDate();
        $endDate   = $this->getEndDate();

        $paramsTo = (new ParamsTo())
            ->setStatName(StatsEnum::AVERAGE_POST_LENGTH)
            ->setStartDate($startDate)
            ->setEndDate($endDate);

        $calculator = new \Statistics\Calculator\AveragePostLength();
        $calculator->setParameters($paramsTo);

        foreach ($this->posts as $post) {
            if (!$post instanceof SocialPostTo)
                continue;

            $calculator->accumulateData($post);
        }

        $result = $calculator->calculate();

        $this->assertEquals(495.25, $result->getValue());
    }

    /**
     * @test
     */
    public function testCalculateMaxPostLength(): void
    {

        $startDate = $this->getStartDate();
        $endDate   = $this->getEndDate();

        $paramsTo = (new ParamsTo())
            ->setStatName(StatsEnum::MAX_POST_LENGTH)
            ->setStartDate($startDate)
            ->setEndDate($endDate);

        $calculator = new \Statistics\Calculator\MaxPostLength();
        $calculator->setParameters($paramsTo);

        foreach ($this->posts as $post) {
            if (!$post instanceof SocialPostTo)
                continue;

            $calculator->accumulateData($post);
        }

        $result = $calculator->calculate();

        $this->assertEquals(638, $result->getValue());
    }

    /**
     * @test
     */
    public function testCalculateAveragePostsPerUser(): void
    {

        $startDate = $this->getStartDate();
        $endDate   = $this->getEndDate();

        $paramsTo = (new ParamsTo())
            ->setStatName(StatsEnum::AVERAGE_POST_NUMBER_PER_USER)
            ->setStartDate($startDate)
            ->setEndDate($endDate);

        $calculator = new \Statistics\Calculator\AveragePostsPerUser();
        $calculator->setParameters($paramsTo);

        foreach ($this->posts as $post) {
            if (!$post instanceof SocialPostTo)
                continue;

            $calculator->accumulateData($post);
        }

        $result = $calculator->calculate();

        $this->assertEquals(1, $result->getValue());
    }

    protected function getStartDate(): \DateTime
    {
        return (clone $this->date)->modify('first day of this month');
    }

    protected function getEndDate(): \DateTime
    {
        return (clone $this->date)->modify('last day of this month');
    }

    protected function getFakeFictionalDriver(): FictionalDriver
    {
        $fakeFictionalDriver = $this->createPartialMock(FictionalDriver::class, ['retrievePage', 'getAccessToken']);
        $fakeFictionalDriver->method('retrievePage')
            ->willReturn($this->getSocialPostsResponseJson());

        $fakeFictionalDriver->method('getAccessToken')
            ->willReturn(uniqid());

        return $fakeFictionalDriver;
    }

    protected function getSocialPostsResponse(): string
    {
        return file_get_contents(__DIR__ . '/../data/social-posts-response.json') ?? '[]';
    }

    protected function getSocialPostsResponseJson(): array
    {
        return json_decode($this->getSocialPostsResponse(), true);
    }

    public static function callMethod($obj, $name, array $args = []) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}
