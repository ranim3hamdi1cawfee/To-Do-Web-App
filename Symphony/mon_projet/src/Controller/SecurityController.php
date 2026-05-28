<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        GroupRepository $groupRepository
    ): Response {
        $error = null;
        $success = null;

        $groups = $groupRepository->findBy([], ['name' => 'ASC']);

        if ($request->isMethod('POST')) {
            $username   = trim($request->request->get('username', ''));
            $password   = $request->request->get('password', '');
            $birthday   = $request->request->get('birthday', '');
            $adminCode  = trim($request->request->get('admin_code', ''));
            $groupId    = $request->request->get('group_id');

            $adminSecret = $_ENV['ADMIN_SECRET_CODE'] ?? 'TF-ADMIN-2026';

            // Validate admin code if provided
            if ($adminCode !== '' && $adminCode !== $adminSecret) {
                $error = 'Invalid admin code.';
            } elseif (empty($username) || empty($password) || empty($birthday)) {
                $error = 'All mandatory fields are required.';
            } else {
                // Check if username already taken
                $existing = $em->getRepository(User::class)->findOneBy(['username' => $username]);
                if ($existing) {
                    $error = 'Username already taken. Please choose another.';
                } else {
                    $user = new User();
                    $user->setUsername($username);
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
                    $user->setBirthday(new \DateTime($birthday));
                    $user->setRole($adminCode === $adminSecret && $adminCode !== '' ? 'Admin' : 'Regular');

                    if ($groupId) {
                        $group = $groupRepository->find((int)$groupId);
                        $user->setGroup($group);
                    }

                    $em->persist($user);
                    $em->flush();

                    $success = 'Account created successfully!';
                }
            }
        }

        return $this->render('security/register.html.twig', [
            'groups' => $groups,
            'error'  => $error,
            'success' => $success,
        ]);
    }
}