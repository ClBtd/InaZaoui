<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private Generator $faker;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        Generator $faker,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->faker = $faker;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création de l'utilisateur admin
        $admin = (new User())
            ->setEmail('admin@example.com')
            ->setName('admin')
            ->setPassword($this->userPasswordHasher->hashPassword(new User(), 'password'))
            ->setDescription($this->faker->paragraph)
            ->setRoles(['ROLE_ADMIN'])
            ->setAccess(true);

        // Persistance de l'admin
        $manager->persist($admin);

        // Création de 5 utilisateurs
        for ($i = 1; $i <= 5; $i++) {
            $user = (new User())
                ->setEmail(sprintf('user%d@example.com', $i))
                ->setName(sprintf('user%d', $i))
                ->setPassword($this->userPasswordHasher->hashPassword(new User(), 'password'))
                ->setDescription($this->faker->paragraph)
                ->setRoles(['ROLE_USER'])
                ->setAccess(true);

            // Persistance de chaque utilisateur
            $manager->persist($user);
        }

        // Enregistrement des données dans la base de données
        $manager->flush();
    }
}
