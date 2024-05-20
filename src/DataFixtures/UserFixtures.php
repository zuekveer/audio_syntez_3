<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; ++$i) {
            $user = (new Users())
                ->setGuid('Test user'.$i)
                ->setRole('Test user'.$i.'Standard')
                ->setName('Test user name'.$i)
                ->setPassword('Test user pass'.$i.'1234')
                ->setEmail('Test user'.$i.'@email.com')
                ->setPhone('Test user number'.$i)
                ->setVerified(true)
                ->setToken(123456)
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());
            $manager->persist($user);
        }
        $manager->flush();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public static function create(ContainerInterface $container, string $email, string $phone): Users
    {
        $user = (new Users())
            ->setName('DummyName')
            ->setPhone($phone)
            ->setEmail($email)
            ->setWallet(new Wallet())
            ->setToken(012345)
            ->setRole('ROLE_ADMIN');
        $user->setPassword($container->get('security.user_password_hasher')->hashPassword($user, 'TestUserPass01234!'));

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->persist($user->getWallet());
        $manager->flush();

        return $user;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function destruct(ContainerInterface $container, string $email): void
    {
        $manager = $container
            ->get('doctrine')
            ->getManager();

        $user = $manager
            ->getRepository(Users::class)
            ->findOneBy(['email' => $email]);

        $manager->remove($user);
        $manager->remove($user->getWallet());
        $manager->flush();
    }
}
