<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use App\Factory\TodoListFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->createOne([
            'email' => 'ante_admin@example.com',
            'roles' => ['ROLE_ADMIN'],
        ]);

        UserFactory::new()->createOne([
            'email' => 'ante_user@example.com',
        ]);

        UserFactory::new()->createMany(10);

        TodoListFactory::new()->createMany(20,function (){
            return [
                'user' => UserFactory::random(),
            ];
        });

        TaskFactory::new()->createMany(40, function(){
            return[
                'todoList' => TodoListFactory::random(),
            ];
        });
    }
}
