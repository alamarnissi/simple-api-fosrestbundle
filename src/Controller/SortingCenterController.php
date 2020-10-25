<?php

namespace App\Controller;

use App\Entity\SortingCenter;
use App\Form\SortingCenterType;
use App\Repository\SortingCenterRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="center_api_")
 */
class SortingCenterController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/centers")
     * @param SortingCenterRepository $sortingCenterRepository
     * @return Response
     */
    public function index(SortingCenterRepository $sortingCenterRepository): Response
    {
        $centers = $sortingCenterRepository->findAll();

        return $this->handleView($this->view($centers));
    }

    /**
     * @Rest\Post("/center")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $sortingCenter = new SortingCenter();
        $form = $this->createForm(SortingCenterType::class, $sortingCenter);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortingCenter);
            $entityManager->flush();

            return $this->handleView($this->view(['status' => true, 'message' => 'Center Inserted successfully'], Response::HTTP_CREATED));
        }


        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Rest\Get("/center/{id}")
     * @param $id
     * @param SortingCenterRepository $sortingCenterRepository
     * @return Response
     */
    public function show($id, SortingCenterRepository $sortingCenterRepository): Response
    {
        $center = $sortingCenterRepository->findOneBy(array(
            'id' => $id
        ));

        if (empty($center))
        {
            return $this->handleView($this->view(['status' => false , 'message' => 'Pas de centre trouvé avec l\'ID :'.$id], Response::HTTP_BAD_REQUEST));
        }

        return $this->handleView($this->view($center));

    }

    /**
     * @Rest\Put("/center/{id}")
     * @param Request $request
     * @param $id
     * @param SortingCenterRepository $sortingCenterRepository
     * @return Response
     */
    public function edit(Request $request, $id, SortingCenterRepository $sortingCenterRepository): Response
    {
        $center = $sortingCenterRepository->findOneBy(array(
            'id' => $id
        ));

        if (empty($center))
        {
            return $this->handleView($this->view(['status' => false , 'message' => 'Pas de centre trouvé avec l\'ID :'.$id], Response::HTTP_BAD_REQUEST));
        }

        $form = $this->createForm(SortingCenterType::class, $center);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->handleview($this->view(['status' => true, 'message' => 'Center Updated successfully'], Response::HTTP_CREATED));
        }

        return $this->handleview($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Rest\Delete("/center/{id}")
     * @param $id
     * @param SortingCenterRepository $sortingCenterRepository
     * @return Response
     */
    public function delete($id, SortingCenterRepository $sortingCenterRepository): Response
    {
        $center = $sortingCenterRepository->findOneBy(array(
            'id' => $id
        ));

        if (empty($center))
        {
            return $this->handleView($this->view(['status' => false , 'message' => 'Pas de centre trouvé avec l\'ID :'.$id], Response::HTTP_BAD_REQUEST));
        }

        if ($center) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($center);
            $entityManager->flush();
        }

        return $this->handleView($this->view(['status' => true, 'message' => 'Center deleted successfully'], Response::HTTP_OK));
    }
}
