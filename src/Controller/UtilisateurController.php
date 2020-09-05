<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use App\Service\FileUploader;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api", name="user_api_")
 */
class UtilisateurController extends AbstractFOSRestController
{
    /**
     * @Route("/", name="dashbord", defaults={"reactRouting": null})
     */
    public function dashbord()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Rest\Get("/users")
     */
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $users = $utilisateurRepository->findAll();
        return $this->handleView($this->view($users));

    }

    /**
     * @Rest\Post("/user")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function new(Request $request, FileUploader $fileUploader, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $data = json_decode($request->getContent(), true);
        $imgFile = $request->request->get('image');
        $password = json_decode($request->request->get('password'));

        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
                if ($imgFile)
                {
                    //return $this->handleView($this->view($imgFile));
                    $imgName = $fileUploader->upload($imgFile);
                    $utilisateur->setImage($imgName);
                }

                if ($password)
                {
                    $encoded = $passwordEncoder->encodePassword(
                        $utilisateur,
                        $password
                    );
                    $utilisateur->setPassword($encoded);
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($utilisateur);
                $entityManager->flush();

                return $this->handleview($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Rest\Get("/user/{id}")
     * @param Utilisateur $utilisateur
     * @return Response
     */
    public function show(UtilisateurRepository $utilisateurRepo, $id): Response
    {
        $utilisateur = $utilisateurRepo->findOneBy([
            'id' => $id
        ]);
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * @Rest\Put("/user/{id}")
     */
    public function edit(Request $request, Utilisateur $utilisateur): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_api_app_utilisateur_index');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Rest\Delete("/user/{id}")
     */
    public function delete(Request $request, $id): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $utilisateur = $entityManager->getRepository(Utilisateur::class)
                            ->findOneBy([
                                'id' => $id
                            ]);
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('USER_API_APP_UTILISATEUR_INDEX');
    }
}
