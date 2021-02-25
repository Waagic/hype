<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/", name="game_index", methods={"GET"})
     * @param GameRepository $gameRepository
     * @return Response
     */
    public function index(GameRepository $gameRepository): Response
    {
        if ($this->getUser()) {
            $userGames = $this->getUser()->getGames();
        } else {
            $userGames = null;
        }
        return $this->render('game/index.html.twig', [
            'jeux' => $gameRepository->findBy([

                ],
                ['id' => 'DESC']
            ),
            'userJeux' => $userGames,
        ]);
    }

    /**
     * @Route("/new", name="game_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param Slugify $slugify
     * @return Response
     */
    public function new(Request $request, FileUploader $fileUploader, Slugify $slugify): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugify->generate($game->getName());
            $game->setSlug($slug);

            $cover = $form->get('cover')->getData();
            if ($cover) {
                $pictureFileName = $fileUploader->upload($cover, $this->getParameter('covers_directory'));
                $game->setCover($pictureFileName);
            }

            $thumbnail = $form->get('thumbnail')->getData();
            if ($thumbnail) {
                $pictureFileName = $fileUploader->upload($thumbnail, $this->getParameter('thumbnails_directory'));
                $game->setThumbnail($pictureFileName);
            }

            $screenshots = $form->get('screenshot')->all();
            if ($screenshots) {
                foreach ($screenshots as $screenshot) {
                    $pictureFileName = $fileUploader->upload($screenshot->get('fileUpload')->getData(), $this->getParameter('screenshots_directory'));
                    $screenshot->getData()->setFile($pictureFileName);
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('game_index');
        }

        return $this->render('game/new.html.twig', [
            'jeu' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="game_show", methods={"GET"})
     * @param Game $game
     * @return Response
     */
    public function show(Game $game): Response
    {
        $user = $this->getUser();
        if ($user) {
            $like = $user->isLiked($game);
        } else {
            $like = 0;
        }
        return $this->render('game/show.html.twig', [
            'jeu' => $game,
            'like' => $like,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="game_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Game $game
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function edit(Request $request, Game $game, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $screenshots = $form->get('screenshot')->all();

            $cover = $form->get('cover')->getData();
            if ($cover) {
                $pictureFileName = $fileUploader->upload($cover, $this->getParameter('covers_directory'));
                $game->setCover($pictureFileName);
            }

            $thumbnail = $form->get('thumbnail')->getData();
            if ($thumbnail) {
                $pictureFileName = $fileUploader->upload($thumbnail, $this->getParameter('thumbnails_directory'));
                $game->setThumbnail($pictureFileName);
            }

            foreach ($screenshots as $screenshot) {
                $fileUpload = $screenshot->get('fileUpload')->getData();
                if ($fileUpload) {
                    $pictureFileName = $fileUploader->upload($fileUpload, $this->getParameter('screenshots_directory'));
                    $screenshot->getData()->setFile($pictureFileName);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_index');
        }

        return $this->render('game/edit.html.twig', [
            'jeu' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="game_delete", methods={"DELETE"})
     * @param Request $request
     * @param Game $game
     * @return Response
     */
    public function delete(Request $request, Game $game): Response
    {
        if ($this->isCsrfTokenValid('delete' . $game->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_index');
    }

    /**
     * @Route("/like/{id}", name="game_like", methods={"GET"})
     * @param Game $game
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @return Response
     */
    public function like(Game $game, UserRepository $userRepository, UserInterface $user): Response
    {
        $user = $this->getUser();
        $user->addGames($game);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute("game_show", [
            "slug" => $game->getSlug(),
        ]);
    }

    /**
     * @Route("/unlike/{id}", name="game_unlike", methods={"GET"})
     * @param Game $game
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @return Response
     */
    public function unlike(Game $game, UserRepository $userRepository, UserInterface $user): Response
    {
        $user = $this->getUser();
        $user->removeGames($game);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute("game_show", [
            "slug" => $game->getSlug(),
        ]);
    }
}
