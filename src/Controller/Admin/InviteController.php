<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class InviteController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('admin/invite', name: 'admin_invite_index')]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 25;

        $total = $this->entityManager->getRepository(User::class)->count([]);
        $totalPages = ceil($total / $limit);

        $page = min($page, $totalPages);
        $offset = $limit * ($page - 1);

        $guests = $this->entityManager->getRepository(User::class)->findBy(
            [],
            ["id" => "ASC"],
            $limit,
            $offset
        );

        return $this->render('admin/invite/index.html.twig', [
            'guests' => $guests,
            'total' => $total,
            'page' => $page
        ]);
    }

    #[Route('admin/invite/add', name: 'admin_invite_add')]
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $user->setAccess(true);
            $password = $form->get('password')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Invité ajouté avec succès.');

            return $this->redirectToRoute('admin_invite_index');
        }

        return $this->render('admin/invite/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route('admin/invite/access/{id<\d+>}', name: 'admin_invite_access')]
    public function guestAccess(int $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $user->setAccess(!$user->isAccess());
        $this->entityManager->flush();

        $this->addFlash('success', 'Accès de l’invité modifié avec succès.');

        return $this->redirectToRoute('admin_invite_index');
    }

    #[Route('admin/invite/delete/{id<\d+>}', name: 'admin_invite_delete')]
    public function delete(int $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        if ($user === $this->getUser()) {
            throw new \LogicException('Vous ne pouvez pas vous supprimer vous-même.');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'Invité supprimé avec succès.');

        return $this->redirectToRoute('admin_invite_index');
    }
}
