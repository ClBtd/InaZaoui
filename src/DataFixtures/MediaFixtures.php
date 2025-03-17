<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class MediaFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    public function load(ObjectManager $manager): void
    {
        // Création de 5 albums
        for ($i = 1; $i <= 5; $i++) {
            $manager->persist(
                (new Album())
                    ->setName(sprintf('album%d', $i))
            );
        }

        $manager->flush();

        // Récupération des albums et de l'utilisateur admin
        $albums = $manager->getRepository(Album::class)->findBy([]);
        $admin = $manager->getRepository(User::class)->findAdmin();

        // Création de 15 médias associés à l'admin
        for ($i = 1; $i <= 15; $i++) {
            $manager->persist(
                (new Media())
                    ->setUser($admin)
                    ->setPath(sprintf('uploads/admin_media%d.jpg', $i))
                    ->setTitle(sprintf('admin_media%d', $i))
                    ->setAlbum($this->faker->randomElement($albums))
            );
        }

        // Récupération des utilisateurs non-admins
        $users = $manager->getRepository(User::class)->findNonAdmins();

        // Création de 60 médias associés à des utilisateurs non-admins
        for ($i = 1; $i <= 60; $i++) {
            $manager->persist(
                (new Media())
                    ->setUser($this->faker->randomElement($users))
                    ->setPath(sprintf('uploads/user_media%d.jpg', $i))
                    ->setTitle(sprintf('user_media%d', $i))
            );
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
