<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixtures;
use App\Entity\Users;
use App\Tests\Provider\Provider;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function setUpBeforeClass(): void
    {
        $client = static::createClient();
        /**
         * @var ManagerRegistry $doctrine
         */
        $doctrine = $client->getContainer()->get('doctrine');
        if (null !== $doctrine
                ->getManager()
                ->getRepository(Users::class)
                ->findOneBy(['email' => 'test@test.com'])
        ) {
            UserFixtures::destruct($client->getContainer(), 'test@test.com');
        }
    }

    public function setUp(): void
    {
        $this->tearDown();
    }

    #[DataProviderExternal(Provider::class, 'registrationProvider')]
    public function testRegister(string $name, string $email, string $phone, string $password, int $code): void
    {
        $data = ['name' => $name, 'email' => $email, 'phone' => $phone, 'password' => $password];

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode($data);

        $client = $this->createClient();
        $client->request(
            'POST',
            '/api/registration',
            [],
            [],
            ['CONTENT_TYPE' => 'application_json'],
            $jsonData
        );

        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }

    #[DataProviderExternal(Provider::class, 'resetPasswordProvider')]
    public function testResetPassword(string $email, int $code): void
    {
        $client = $this->createClient();

        /**
         * @var ManagerRegistry $doctrine
         */
        $doctrine = $client->getContainer()->get('doctrine');

        /**
         * @var Users $user
         */
        $user = $doctrine
            ->getManager()
            ->getRepository(Users::class)
            ->findOneBy(['email' => 'test@test.com']);

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode(['email' => $user->getEmail()]);

        $client->request(
            'POST',
            '/api/reset',
            [],
            [],
            ['CONTENT_TYPE' => 'application_json'],
            $jsonData
        );

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode(['email' => $email, 'token' => $user->getToken(), 'password' => 'TestPassUser01234!']);

        $client->request(
            'POST',
            '/api/reset',
            [],
            [],
            ['CONTENT_TYPE' => 'application_json'],
            $jsonData
        );

        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }

    #[DataProviderExternal(Provider::class, 'setNewPasswordProvider')]
    public function testSendForReset(string $email, int $code): void
    {
        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode(['email' => $email]);

        $client = $this->createClient();
        $client->request(
            'POST',
            '/api/reset',
            [],
            [],
            ['CONTENT_TYPE' => 'application_json'],
            $jsonData
        );

        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DataProviderExternal(Provider::class, 'verifyCodeProvider')]
    public function testVerify(string $email, int $code): void
    {
        $client = $this->createClient();
        $container = $client->getContainer();
        UserFixtures::create($container, 'test@test.com', '0123456789');

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode(['email' => $email, 'token' => 012345]);
        $client->request(
            'POST',
            '/api/verify-code',
            [],
            [],
            ['CONTENT_TYPE' => 'application_json'],
            $jsonData
        );

        UserFixtures::destruct($client->getContainer(), 'tes@test.com');
        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }

    #[DataProviderExternal(Provider::class, 'loginProvider')]
    public function testLogin(string $email, string $password, int $code): void
    {
        $client = $this->createClient();

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode(['email' => $email, 'password' => $password]);
        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }
}
