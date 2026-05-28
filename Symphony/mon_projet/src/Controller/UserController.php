<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    // -------------------------------------------------------
    // PROFILE
    // -------------------------------------------------------
    #[Route('/user/{id}', name: 'app_user_profile', requirements: ['id' => '\d+'])]
    public function profile(int $id, UserRepository $userRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $userRepo->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        $joinedDate        = $user->getCreatedAt()->format('F Y');
        $birthdayFormatted = $user->getBirthday()?->format('F d, Y') ?? 'Not Specified';

        return $this->render('user/profile.html.twig', [
            'profileUser'       => $user,
            'joinedDate'        => $joinedDate,
            'birthdayFormatted' => $birthdayFormatted,
            'isSameUserOrAdmin' => $this->isSameUserOrAdmin($user),
        ]);
    }

    // -------------------------------------------------------
    // EDIT
    // -------------------------------------------------------
    #[Route('/user/{id}/edit', name: 'app_user_edit', requirements: ['id' => '\d+'])]
    public function edit(
        int $id,
        Request $request,
        UserRepository $userRepo,
        GroupRepository $groupRepo,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $userRepo->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        if (!$this->isSameUserOrAdmin($user)) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $groups = $this->isGranted('ROLE_ADMIN') ? $groupRepo->findBy([], ['name' => 'ASC']) : [];

        if ($request->isMethod('POST')) {
            $user->setUsername(trim($request->request->get('username')));
            $user->setMotto(trim($request->request->get('motto')));

            // Handle avatar upload
            $avatarFile = $request->files->get('avatar');
            if ($avatarFile) {
                $uploadDir  = $this->getParameter('kernel.project_dir') . '/public/uploads/';
                $newFilename = time() . '_' . $avatarFile->getClientOriginalName();
                $avatarFile->move($uploadDir, $newFilename);
                $user->setProfileImage($newFilename);
            }

            // Admin-only fields
            if ($this->isGranted('ROLE_ADMIN')) {
                $user->setRole($request->request->get('role', $user->getRole()));
                $groupId = $request->request->get('group_id');
                $user->setGroup($groupId ? $groupRepo->find((int)$groupId) : null);
            }

            $em->flush();
            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }

        $previewSrc = ($user->getProfileImage() && $user->getProfileImage() !== 'default_avatar.png')
            ? 'uploads/' . $user->getProfileImage()
            : 'uploads/default_avatar.png';

        return $this->render('user/edit.html.twig', [
            'profileUser' => $user,
            'groups'      => $groups,
            'previewSrc'  => $previewSrc,
        ]);
    }

    // -------------------------------------------------------
    // DELETE
    // -------------------------------------------------------
    #[Route('/user/{id}/delete', name: 'app_user_delete', requirements: ['id' => '\d+'])]
    public function delete(
        int $id,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $userRepo->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        if (!$this->isSameUserOrAdmin($user)) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $isSelf = $this->getUser() === $user;

        $em->remove($user);
        $em->flush();

        if ($isSelf) {
            // Invalidate the session immediately so Symfony stops trying to reload the deleted user
            $request->getSession()->invalidate();
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('app_login');
    }

    // -------------------------------------------------------
    // HELPER
    // -------------------------------------------------------
    private function isSameUserOrAdmin(User $target): bool
    {
        return $this->isGranted('ROLE_ADMIN') || $this->getUser() === $target;
    }
}
