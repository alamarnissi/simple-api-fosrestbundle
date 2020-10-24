<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="product_api_")
 */
class ProduitController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/products")
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        $products = $produitRepository->findAll();
        return $this->handleView($this->view($products));
    }

    /**
     * @Rest\Post("/product")
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $data = json_decode($request->getContent(), true);

        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->handleView($this->view(['status' => true, 'message' => 'Product inserted successfully'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Rest\Get("/product/{id}")
     */
    public function show($id, ProduitRepository $produitRepository): Response
    {
        $product = $produitRepository->findOneBy(array(
            "id" => $id
        ));

        if (empty($product))
        {
            return $this->handleView($this->view(['status' => false, 'message' => 'Pas de produit trouvé avec l\'ID :'.$id], Response::HTTP_BAD_REQUEST));
        }

        return $this->handleView($this->view($product));
    }

    /**
     * @Rest\Put("/product/{id}")
     */
    public function edit(Request $request, $id, ProduitRepository $produitRepository): Response
    {
        $product = $produitRepository->findOneBy(array(
            "id" => $id
        ));

        if (empty($product))
        {
            return $this->handleView($this->view(['status' => false, 'message' => 'Pas de produit trouvé avec l\'ID :'.$id], Response::HTTP_BAD_REQUEST));
        }

        $form = $this->createForm(ProduitType::class, $product);
        $data = json_decode($request->getContent(), true);

        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->handleview($this->view(['status' => true, 'message' => 'Product Updated successfully'], Response::HTTP_CREATED));
        }
        return $this->handleview($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));

    }

    /**
     * @Rest\Delete("/product/{id}")
     */
    public function delete(Request $request, $id, ProduitRepository $produitRepository): Response
    {
        $product = $produitRepository->findOneBy(array(
            "id" => $id
        ));

        if (empty($product))
        {
            return $this->handleView($this->view(['status' => false, 'message' => 'Pas de produit trouvé avec l\'ID :'.$id], Response::HTTP_BAD_REQUEST));
        }

        if ($product) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->handleView($this->view(['status' => true, 'message' => 'Product deleted successfully'], Response::HTTP_OK));
    }
}
