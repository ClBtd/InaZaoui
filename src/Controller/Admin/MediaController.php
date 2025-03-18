<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\User;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/media', name: 'admin_media_index')]
    public function index(Request $request) : Response
    {
        // Récupère la page courante depuis la requête
        $page = $request->query->getInt('page', 1);
        $criteria = [];

        // Si l'utilisateur n'est pas admin, on filtre les médias par utilisateur
        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
        }

        // Compte le nombre total de médias selon les critères
        $total = $this->entityManager->getRepository(Media::class)->count($criteria);
        $limit = 25;
        $totalPages = ceil($total / $limit);
        $page = max(1, min($request->query->getInt('page', 1), $totalPages));
        $offset = $limit * ($page - 1);

        // Récupère les médias avec pagination
        $medias = $this->entityManager->getRepository(Media::class)->findBy(
            $criteria ?: [],
            ['id' => 'ASC'],
            $limit,
            $offset
        );

        // Rendu de la page avec les médias
        return $this->render('admin/media/index.html.twig', [
            'medias' => $medias,
            'total' => $total,
            'page' => $page,
        ]);
    }

    #[Route('/admin/media/add', name: 'admin_media_add')]
    public function add(Request $request) : Response
    {
        $media = new Media(); // Création d'un objet Media
        $form = $this->createForm(MediaType::class, $media, [
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Si non-admin, associer le média à l'utilisateur courant
            if (!$this->isGranted('ROLE_ADMIN')) {
                $media->setUser($this->getUser() instanceof User ? $this->getUser() : null);
            }

            // Générer un chemin unique pour le fichier
            $media->setPath('uploads/' . md5(uniqid()) . '.' . $media->getFile()->guessExtension());
            $media->getFile()->move('uploads/', $media->getPath());

            // Enregistrer le média en base
            $this->entityManager->persist($media);
            $this->entityManager->flush();

            // Redirection vers la page des médias
            return $this->redirectToRoute('admin_media_index');
        }

        // Rendu du formulaire d'ajout de média
        return $this->render('admin/media/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/admin/media/delete/{id<\d+>}', name: 'admin_media_delete')]
    public function delete(int $id) : Response
    {
        // Vérification de l'existence du média
        $media = $this->entityManager->getRepository(Media::class)->find($id);

        // Si le média n'existe pas, on redirige
        if (!$media) {
            return $this->redirectToRoute('admin_media_index');
        }

        // Suppression du média en base de données
        $this->entityManager->remove($media);
        $this->entityManager->flush();

        // Suppression du fichier associé
        if (file_exists($media->getPath())) {
            unlink($media->getPath());
        }

        // Redirection vers la page des médias
        return $this->redirectToRoute('admin_media_index');
    }
}
