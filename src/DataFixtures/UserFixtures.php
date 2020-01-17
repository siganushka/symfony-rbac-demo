<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setUsername('admin');
        $user1->setRole($this->getReference('role1'));

        $user2 = new User();
        $user2->setUsername('zhangsan');
        $user2->setRole($this->getReference('role2'));

        $user3 = new User();
        $user3->setUsername('lisi');
        $user3->setRole($this->getReference('role3'));

        $this->applyPassword($user1);
        $this->applyPassword($user2);
        $this->applyPassword($user3);

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->persist($user3);
        $manager->flush();

        $this->addReference('user1', $user1);
        $this->addReference('user2', $user2);
        $this->addReference('user3', $user3);
    }

    public function getDependencies()
    {
        return [
            RoleFixtures::class,
        ];
    }

    private function applyPassword(User $user)
    {
        $password = $this->passwordEncoder->encodePassword($user, '123456');
        $user->setPassword($password);
    }
}
