<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $role1 = new Role();
        $role1->setName('超级管理员');
        $role1->setNodes([
            'app_role',
            'app_role_new',
            'app_role_edit',
            'app_role_delete',
            'app_user',
            'app_user_new',
            'app_user_edit',
            'app_user_delete',
        ]);

        $role2 = new Role();
        $role2->setName('管理员');
        $role2->setNodes([
            'app_user',
            'app_user_new',
            'app_user_edit',
            'app_user_delete',
        ]);

        $role3 = new Role();
        $role3->setName('用户');
        $role3->setNodes([
            'app_user',
        ]);

        $manager->persist($role1);
        $manager->persist($role2);
        $manager->persist($role3);
        $manager->flush();

        $this->addReference('role1', $role1);
        $this->addReference('role2', $role2);
        $this->addReference('role3', $role3);
    }
}
