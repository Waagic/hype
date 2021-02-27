<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/users", name="profile_users", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function users(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $userRepository->findBy([
            'isOk' => 0
        ]);
        return $this->render('profile/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/profile/okusers/{id}", name="profile_okusers", methods={"GET"})
     * @param User $user
     * @param UserRepository $userRepository
     * @return Response
     */
    public function okUsers(User $user, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user->setIsOk(1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute('profile_users');
    }
}