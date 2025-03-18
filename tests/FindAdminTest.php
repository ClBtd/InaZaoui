<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FindAdminTest extends WebTestCase
{
    // Test pour vérifier que l'utilisateur admin a bien les rôles attendus
    public function testShouldOnlyFindAdminUser() : void
    {
        $userRepository = self::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findAdmin();

        $this->assertSame(["ROLE_ADMIN", "ROLE_USER"], $user->getRoles());
    }
}
