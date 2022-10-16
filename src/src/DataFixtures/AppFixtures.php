<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\UserFactory;
use App\Factory\TagFactory;
use App\Factory\PostFactory;
use App\Factory\QuestionFactory;
use App\Factory\VoteFactory;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(15);
        TagFactory::createMany(10);
        PostFactory::createMany(30);
        QuestionFactory::createMany(30);
        VoteFactory::createMany(100);

        // nous allons créer un utilisateur admin
        UserFactory::new()->create([
            'email' => 'admin@admin.com',
            'lastname' => 'admin',
            'firstname' => 'admin',
            'adresse' => 'NETWORK',
            'roles' => ['ROLE_ADMIN'],
            'password' => 'admin',
        ]);

        $manager->flush();
    }
}
