<?php

namespace App\Controller;

use App\Entity\Jeu;
use App\Form\JeuType;
use App\Repository\JeuRepository;
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
class JeuController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
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
     * @Route("/", name="jeu_index", methods={"GET"})
     * @param JeuRepository $jeuRepository
     * @return Response
     */
    public function index(JeuRepository $jeuRepository): Response
    {
        return $this->render('jeu/index.html.twig', [
            'jeux' => $jeuRepository->findAll(),
            'userJeux' => $this->getUser()->getJeux(),
        ]);
    }

    /**
     * @Route("/new", name="jeu_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param Slugify $slugify
     * @return Response
     */
    public function new(Request $request, FileUploader $fileUploader, Slugify $slugify): Response
    {
        $jeu = new Jeu();
        $form = $this->createForm(JeuType::class, $jeu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugify->generate($jeu->getNom());
            $jeu->setSlug($slug);

            $cover = $form->get('cover')->getData();
            if ($cover) {
                $pictureFileName = $fileUploader->upload($cover, $this->getParameter('covers_directory'));
                $jeu->setCover($pictureFileName);
            }

            $thumbnail = $form->get('thumbnail')->getData();
            if ($thumbnail) {
                $pictureFileName = $fileUploader->upload($thumbnail, $this->getParameter('thumbnails_directory'));
                $jeu->setThumbnail($pictureFileName);
            }

            $screenshots = $form->get('screenshot')->all();
            if ($screenshots) {
                foreach ($screenshots as $screenshot) {
                    $pictureFileName = $fileUploader->upload($screenshot->get('fileUpload')->getData(), $this->getParameter('screenshots_directory'));
                    $screenshot->getData()->setFile($pictureFileName);
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($jeu);
            $entityManager->flush();

            return $this->redirectToRoute('jeu_index');
        }

        return $this->render('jeu/new.html.twig', [
            'jeu' => $jeu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="jeu_show", methods={"GET"})
     * @param Jeu $jeu
     * @return Response
     */
    public function show(Jeu $jeu): Response
    {
        $user = $this->getUser();
        $like = $user->isLiked($jeu);
        return $this->render('jeu/show.html.twig', [
            'jeu' => $jeu,
            'like' => $like,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="jeu_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Jeu $jeu
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function edit(Request $request, Jeu $jeu, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(JeuType::class, $jeu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $screenshots = $form->get('screenshot')->all();

            $cover = $form->get('cover')->getData();
            if ($cover) {
                $pictureFileName = $fileUploader->upload($cover, $this->getParameter('covers_directory'));
                $jeu->setCover($pictureFileName);
            }

            $thumbnail = $form->get('thumbnail')->getData();
            if ($thumbnail) {
                $pictureFileName = $fileUploader->upload($thumbnail, $this->getParameter('thumbnails_directory'));
                $jeu->setThumbnail($pictureFileName);
            }

            foreach ($screenshots as $screenshot) {
                $fileUpload = $screenshot->get('fileUpload')->getData();
                if ($fileUpload) {
                    $pictureFileName = $fileUploader->upload($fileUpload, $this->getParameter('screenshots_directory'));
                    $screenshot->getData()->setFile($pictureFileName);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('jeu_index');
        }

        return $this->render('jeu/edit.html.twig', [
            'jeu' => $jeu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="jeu_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Jeu $jeu): Response
    {
        if ($this->isCsrfTokenValid('delete' . $jeu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($jeu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('jeu_index');
    }

    /**
     * @Route("/like/{id}", name="jeu_like", methods={"GET"})
     * @param Jeu $jeu
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @return Response
     */
    public function like(Jeu $jeu, UserRepository $userRepository, UserInterface $user): Response
    {
        $user = $this->getUser();
        $user->addJeux($jeu);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute("jeu_show", [
            "slug" => $jeu->getSlug(),
        ]);
    }

    /**
     * @Route("/unlike/{id}", name="jeu_unlike", methods={"GET"})
     * @param Jeu $jeu
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @return Response
     */
    public function unlike(Jeu $jeu, UserRepository $userRepository, UserInterface $user): Response
    {
        $user = $this->getUser();
        $user->removeJeux($jeu);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute("jeu_show", [
            "slug" => $jeu->getSlug(),
        ]);
    }
}
