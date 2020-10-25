<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use App\Service\FileUploader;
use App\Utils\Reusable;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/", name="user_api_")
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
     * @Rest\Post("/login")
     * @param UtilisateurRepository $utilisateurRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request $request
     * @return Response
     */
    public function login(UtilisateurRepository $utilisateurRepository, UserPasswordEncoderInterface $passwordEncoder, Request $request): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        if ($email && $password)
        {
            $user = $utilisateurRepository->findOneBy([
                'email' => $email
            ]);
            if ($user) {

                return $this->handleView($this->view(['status' => true], Response::HTTP_OK));

            }else{
                return $this->handleView($this->view(['status' => false, 'message' => 'Email is incorrect'], Response::HTTP_BAD_REQUEST));
            }
        }else{
            return $this->handleView($this->view(['status' => false, 'message' => 'Please enter email and password'], Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * @Rest\Get("/users")
     * @param UtilisateurRepository $utilisateurRepository
     * @return Response
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
        $role = $request->request->get('roles');

        $entityManager = $this->getDoctrine()->getManager();

        $checkEmail = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);
        if (!empty($checkEmail)){
            return $this->handleview($this->view(['status' => false, 'message' => 'This email is already taken'], Response::HTTP_BAD_REQUEST));
        }

        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
                if ($imgFile)
                {
                    $imgName = $fileUploader->upload($imgFile);
                    $utilisateur->setImage($imgName);
                }

                if ($role === array('ROLE_USER'))
                {
                    $latitude = $request->request->get('latitude');
                    $longitude = $request->request->get('longitude');

                    if (empty($latitude) || empty($longitude))
                    {
                        return $this->handleView($this->view(['status'=> false ,'message' => 'Veuiller fournir les coordonnés de client']));
                    }
                }

                $entityManager->persist($utilisateur);
                $entityManager->flush();

                return $this->handleview($this->view(['status' => true, 'message' => 'User added successfully'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Rest\Get("/user/{id}")
     * @param UtilisateurRepository $utilisateurRepo
     * @param $id
     * @return Response
     */
    public function show(UtilisateurRepository $utilisateurRepo, $id): Response
    {
        $utilisateur = $utilisateurRepo->findOneBy([
            'id' => $id
        ]);
        return $this->handleView($this->view($utilisateur));

    }

    /**
     * @Rest\Put("/user/{id}")
     * @param Request $request
     * @param $id
     * @param UtilisateurRepository $utilisateurRepo
     * @param FileUploader $fileUploader
     * @param Reusable $reusable
     * @return Response
     */
    public function edit(Request $request, $id, UtilisateurRepository $utilisateurRepo, FileUploader $fileUploader, Reusable $reusable): Response
    {
        $utilisateur = $utilisateurRepo->findOneBy(
            array(
                "id" => $id
            ));
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $data = json_decode($request->getContent(), true);
        $imgFile = $request->request->get('image');

        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imgFile)
            {
                //$currentImg = $reusable->get_string_between($utilisateur->getImage(), '/','.jpeg');
               // $imgPath = $reusable->getConstants($this->container->getParameter('images_directory')).'/'.$currentImg;
                $imgName = $fileUploader->upload($imgFile);
                $utilisateur->setImage($imgName);
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            return $this->handleview($this->view(['status' => true, 'message' => 'User details updated successfully'], Response::HTTP_CREATED));
        }

        return $this->handleview($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));

    }

    /**
     * @Rest\Delete("/user/{id}")
     * @param Request $request
     * @param $id
     * @param UtilisateurRepository $utilisateurRepo
     * @return Response
     */
    public function delete(Request $request, $id, UtilisateurRepository $utilisateurRepo): Response
    {

            $entityManager = $this->getDoctrine()->getManager();
            $utilisateur = $utilisateurRepo->findOneBy(
                            [
                                'id' => $id
                            ]);

        if ($utilisateur)
        {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
            return $this->handleView($this->view(['status' => true, 'message' => 'User deleted successfully'], Response::HTTP_OK));
        }
            return $this->handleView($this->view(['status' => 'Bad Request'], Response::HTTP_BAD_REQUEST));
    }
}
