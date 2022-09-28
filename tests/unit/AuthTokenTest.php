<?php declare(strict_types = 1);

namespace Tests\unit;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use SocialPost\Client\FictionalClient;
use SocialPost\Driver\FictionalDriver;

/**
 * Register token test
 *
 * @package Tests\unit
 */
class AuthTokenTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // Load $_ENV variables
        //$dotEnv = Dotenv::createImmutable(__DIR__ . '/../..');
        //$dotEnv->load();
    }

    /**
     * @test
     */
    public function testFictionalDriverRegisterTokenReturnsToken(): void
    {
        $fakeFictionalClient = $this->createMock(FictionalClient::class);
        $fakeFictionalClient->method('authRequest')
            ->willReturn($this->getFakeAuthTokenResponse());

        $fictionalDriver = new FictionalDriver($fakeFictionalClient);

        $token = static::callMethod($fictionalDriver, 'registerToken');

        $expectedToken = ($this->getFakeAuthTokenResponseJson())['data']['sl_token'] ?? '';

        $this->assertEquals($expectedToken, $token);
    }

    protected function getFakeAuthTokenResponse(): string
    {
        return file_get_contents(__DIR__ . '/../data/auth-token-response.json') ?? '[]';
    }

    protected function getFakeAuthTokenResponseJson(): array
    {
        return json_decode($this->getFakeAuthTokenResponse(), true);
    }

    public static function callMethod($obj, $name, array $args = []) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}
