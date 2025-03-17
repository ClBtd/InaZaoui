<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'home')]
    public function home(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher)
    {
        // Rend la vue d'accueil
        return $this->render('front/home.html.twig');
    }

    #[Route('/guests', name: 'guests')]
    public function guests(UserRepository $userRepository)
    {
        // Récupère les invités avec leur nombre de médias
        $guests = $userRepository->findGuestsWithMediaCount();

        // Rend la vue des invités
        return $this->render('front/guests.html.twig', [
            'guests' => $guests
        ]);
    }

    #[Route('/guest/{id}', name: 'guest')]
    public function guest(int $id)
    {
        // Recherche l'invité par son identifiant
        $guest = $this->entityManager->getRepository(User::class)->find($id);

        // Si l'invité n'est pas trouvé, une erreur 404 peut être lancée (optionnelle)
        if (!$guest) {
            throw $this->createNotFoundException('Invité non trouvé.');
        }

        // Rend la vue de l'invité
        return $this->render('front/guest.html.twig', [
            'guest' => $guest
        ]);
    }

    #[Route('/portfolio/{id}', name: 'portfolio')]
    public function portfolio(?int $id = null)
    {
        // Récupère tous les albums
        $albums = $this->entityManager->getRepository(Album::class)->findAll();

        // Si un identifiant est fourni, récupère l'album correspondant
        $album = $id ? $this->entityManager->getRepository(Album::class)->find($id) : null;

        // Recherche l'utilisateur administrateur
        $user = $this->entityManager->getRepository(User::class)->findAdmin();

        // Récupère les médias associés à l'album ou à l'utilisateur admin
        $medias = $album
            ? $this->entityManager->getRepository(Media::class)->findByAlbum($album)
            : $this->entityManager->getRepository(Media::class)->findByUser($user);

        // Rend la vue du portfolio
        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about()
    {
        // Rend la vue à propos
        return $this->render('front/about.html.twig');
    }
}
