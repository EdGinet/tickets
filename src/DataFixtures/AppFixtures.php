<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Account;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var \Generator
     */
    private Generator $faker;
    

    public function __construct() {
        $this->faker = Factory::create("fr_FR");
    }
    
    public function load(ObjectManager $manager): void
    {
        

        for ($i = 0; $i < 10; $i ++) {
            
            // User

            $user = new User();
            $user->setLastname("Lastname" . $i)
                ->setFirstname("Firstname" .$i)
                ->setPhone($this->faker->phoneNumber())
                ->setCompany("Company" . $i)
                ->setJob("Job" . $i);
            
            $manager->persist($user);

            
            // Account

            $account = new Account();
            $account->setUser($user)
                    ->setEmail($this->faker->unique()->safeEmail())
                    ->setRoles(['ROLE_USER'])
                    ->setPlainPassword('Password');
                    
            $manager->persist($account);
        }

        $manager->flush();
    }
}
