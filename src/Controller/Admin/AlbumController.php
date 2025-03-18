<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Form\AlbumType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Restreint l'accès à ce contrôleur aux utilisateurs ayant le rôle ADMIN
#[IsGranted('ROLE_ADMIN')]
class AlbumController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/album', name: 'admin_album_index')]
    public function index() : Response
    {
        // Récupère tous les albums
        $albums = $this->entityManager->getRepository(Album::class)->findAll();

        // Rend la vue avec la liste des albums
        return $this->render('admin/album/index.html.twig', ['albums' => $albums]);
    }

    #[Route('/admin/album/add', name: 'admin_album_add')]
    public function add(Request $request) : Response
    {
        $album = new Album();
        // Crée le formulaire pour l'entité Album
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, enregistre l'album
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($album);
            $this->entityManager->flush();

            // Redirige vers la liste des albums après l'ajout
            return $this->redirectToRoute('admin_album_index');
        }

        // Rend la vue pour ajouter un album avec le formulaire
        return $this->render('admin/album/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/admin/album/update/{id<\d+>}', name: 'admin_album_update')]
    public function update(Request $request, int $id) : Response
    {
        // Recherche l'album à modifier
        $album = $this->entityManager->getRepository(Album::class)->find($id);

        // Si l'album n'existe pas, lève une exception
        if (!$album) {
            throw $this->createNotFoundException('Album non trouvé.');
        }

        // Crée le formulaire pour l'album trouvé
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, met à jour l'album
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            // Redirige vers la liste des albums après la modification
            return $this->redirectToRoute('admin_album_index');
        }

        // Rend la vue pour modifier l'album avec le formulaire
        return $this->render('admin/album/update.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/admin/album/delete/{id<\d+>}', name: 'admin_album_delete')]
    public function delete(int $id) : Response
    {
        // Recherche l'album à supprimer
        $album = $this->entityManager->getRepository(Album::class)->find($id);

        // Si l'album n'existe pas, lève une exception
        if (!$album) {
            throw $this->createNotFoundException('Album non trouvé.');
        }

        // Supprime l'album de la base de données
        $this->entityManager->remove($album);
        $this->entityManager->flush();

        // Redirige vers la liste des albums après la suppression
        return $this->redirectToRoute('admin_album_index');
    }
}
