<?php

namespace App\Controller;

use App\Entity\Colis;
use App\Entity\ColisProducts;
use App\Entity\Utilisateur;
use App\Form\ColisType;
use App\Form\UtilisateurType;
use App\Repository\ColisProductsRepository;
use App\Repository\ColisRepository;
use Endroid\QrCode\QrCode;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/", name="colis_api_")
 */
class ColisController extends AbstractFOSRestController
{

    public function generateQRCode($id){
        $qrCode = new QrCode($id);
        header('Content-Type: image/png');
        $code = $qrCode->writeDataUri();

        return $code;
    }

    /**
     * @Rest\Get("/qrcode")
     * @param Request $request
     * @param ColisRepository $colisRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function qrcode(Request $request, ColisRepository $colisRepository, SerializerInterface $serializer): Response
    {
        $colisId = $request->query->get('colis');

        $colis = $colisRepository->findOneBy(['id' => $colisId]);

        if(!$colis){
            return $this->handleView($this->view(['status' => false, 'message' => 'No colis with this ID'], Response::HTTP_BAD_REQUEST));
        }

        $qrCode = $this->generateQRCode($colisId);
        $data = $serializer->serialize($colis, 'json', ['groups' => 'colis_details']);
        return $this->handleView($this->view(["qrcode" => $qrCode, "colis" => $data], Response::HTTP_OK));

    }

    /**
     * @Rest\Patch("/colis/incrementStatus/{id}")
     */
    public function incrementStatus($id, Request $request, ColisRepository $colisRepository){
        $colis = $colisRepository->findOneBy(['id' => $id]);
        $isIncremented = $request->query->get('signe');
        $em = $this->getDoctrine()->getManager();

        if(!$colis){
            return $this->handleView($this->view(['status' => false, 'message' => 'No colis with this ID'], Response::HTTP_BAD_REQUEST));
        }

        if(empty($isIncremented)){
            return $this->handleView($this->view(['status' => false, 'message' => 'Please send Colis process sign'], Response::HTTP_BAD_REQUEST));
        }

        $status = $colis->getEtat();

        if ($isIncremented){
            $colis->setEtat($status+1);
            $colis->setSigne(true);
        }else{
            $colis->setEtat($status-1);
            $colis->setSigne(false);
        }

        $em->persist($colis);
        $em->flush();

        return $this->handleView($this->view(['status' => true, 'message' => 'Colis status updated successfully'], Response::HTTP_OK));
    }

    /**
     * @Rest\Get("/colis")
     */
    public function list(ColisRepository $colisRepository, SerializerInterface $serializer): Response
    {
        $colis = $colisRepository->findAll();
        $data = $serializer->serialize($colis, 'json', ['groups' => 'colis_details']);
        return $this->handleView($this->view($data));
    }
    /**
     * @Rest\Post("/colis")
     */
    public function new(Request $request): Response
    {
        $colis = new Colis();
        // send client email after creation
        $clientContent = $request->request->get('client');
        $products = $request->request->get('products');


        $em = $this->getDoctrine()->getManager();

        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);

        if ($products)
        {
            foreach($products as $product){
                $prod = $em->getRepository('App\Entity\Produit')
                    ->findOneBy(array('libelle' => $product['libelle']));

                if (!empty($prod))
                {
                    $colisProd = new ColisProducts();
                    $colisProd->setColis($colis);
                    $colisProd->setProduct($prod);
                    $colisProd->setQuantity($product['qty']);
                }
                $em->persist($colisProd);
            }
            $checkEmail = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $clientContent['email']]);
            if (!empty($checkEmail)){
                return $this->handleview($this->view(['status' => false, 'message' => 'This email is already taken'], Response::HTTP_BAD_REQUEST));
            }
            $form->submit($clientContent);
            if ($form->isSubmitted() && $form->isValid()){
                $em->persist($utilisateur);
                $colis->setClient($utilisateur);
                $em->persist($colis);
            }
            $em->flush();
            return $this->handleView($this->view(['status' => true, 'message' => 'Colis inserted successfully'], Response::HTTP_OK));
        }

        return $this->handleview($this->view(['status' => false, 'message' => 'Bad request'], Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Rest\Get("/colis/{id}")
     */
    public function show($id, ColisRepository $colisRepository, SerializerInterface $serializer): Response
    {
        $colis = $colisRepository->findOneBy(array(
            'id' => $id
        ));
        $data = $serializer->serialize($colis, 'json', ['groups' => 'colis_details']);
        return $this->handleView($this->view($data));
    }

//    /**
//     * @Rest\Put("/colis/{id}")
//     */
//    public function edit(Request $request, $id, ColisRepository $colisRepository): Response
//    {
//        $colis = $colisRepository->findOneBy([
//            "id" => $id
//        ]);
//        $form = $this->createForm(ColisType::class, $colis);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->handleView($this->view(['status' => true, 'message' => 'success], Response::HTTP_OK));
//        }
//
//        return $this->handleview($this->view(['status' => false, 'message' => 'error'], Response::HTTP_BAD_REQUEST));
//    }

    /**
     * @Rest\Delete("/colis/{id}")
     */
    public function delete(Request $request, $id, ColisRepository $colisRepository, ColisProductsRepository $colisProductsRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $colis = $colisRepository->findOneBy([
            "id" => $id
        ]);
        $colisProducts = $colisProductsRepository->findBy([
            "colis" => $colis
        ]);

        if ($colis && $colisProducts) {
            foreach ($colisProducts as $colisProduct)
            {
                $entityManager->remove($colisProduct);
            }
            $entityManager->remove($colis);
            $entityManager->flush();
            return $this->handleView($this->view(['status' => true, 'message' => 'Colis deleted successfully'], Response::HTTP_OK));
        }

        return $this->handleview($this->view(['status' => false, 'message' => 'error'], Response::HTTP_BAD_REQUEST));
    }
}
