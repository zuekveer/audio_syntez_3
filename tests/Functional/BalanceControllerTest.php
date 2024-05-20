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

class BalanceControllerTest extends WebTestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DataProviderExternal(Provider::class, 'putProvider')]
    public function testBalancePUT(string $email, int $amount, int $code): void
    {
        $client = $this->createClient();
        $container = $client->getContainer();

        UserFixtures::create($container, 'test@test.com', '0123456789');

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
            ->findOneBy(['email' => $email]);

        $uuidExample = '381490ef-21a2-4085-9312-7a72baf1733b';
        $id = null === $user ? $uuidExample : $user->getGuid();
        $data = ['amount' => $amount, 'id' => $id];

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode($data);

        if (null !== $user) {
            $client->loginUser($user);
        }

        $client->request(
            'PUT',
            '/api/balance',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        UserFixtures::destruct($container, 'test@test.com');
        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DataProviderExternal(Provider::class, 'getProvider')]
    public function testBalanceGET(string $email, int $code): void
    {
        $client = $this->createClient();
        $container = $client->getContainer();

        UserFixtures::create($container, 'test@test.com', '0123456789');

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
            ->findOneBy(['email' => $email]);

        if (null !== $user) {
            $client->loginUser($user);
        }

        $client->request(
            'GET',
            '/api/balance',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        UserFixtures::destruct($container, 'test@test.com');
        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }
}
