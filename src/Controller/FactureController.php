<?php

namespace App\Controller;

use App\Entity\Factures;
use App\Form\FacturesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController
 * @package App\Controller
 * @Route ("/admin")
 */

class FactureController extends AbstractController
{

    /**
     * @Route("/factures", name="factures")
     */

    public function factures(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Factures::class);

        $orderby=$request->query->get('ORDERBY');
        $mode=$request->query->get('MODE');
        if(!$mode){
            $orderby='id';
            $mode='ASC';
        }
        $headers=['Num Facture'=>'id','Date'=>'date','Client'=>'client.nom','Montant'=>'montant'];
        if($orderby=='client.nom'){
            $values=$repo->orderByClient($orderby,$mode);
        }else{
            $values = $repo->findBy([],[$orderby=>$mode]);
        }
        return $this->render('admin/Factures.html.twig', [
                "i" => '2',
                "factures" => $values,
                'headers'=>$headers,
                'orderby'=>$orderby,
                'mode'=>$mode,
            ]

        );
    }



    /**
     * @Route("/factures/confirmDelete/{id}", name="factures.confirmDelete")
     */

    public function facturesConfirmDelete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $facture = $entityManager->getRepository(Factures::class)->find($id);

        $repo = $this->getDoctrine()->getRepository(Factures::class);
        $factures = $repo->findAll();

        return $this->render('admin/Delete_Factures.html.twig', [
                "i" => '2',
                "factures" => $factures,
                "id" => $id
            ]

        );
    }

    /**
     * @Route("/factures/confirmDelete/{id}/confirmed", name="factures.delete")
     */
    public function deleteFacture($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $facture = $entityManager->getRepository(Factures::class)->find($id);
        $this->addFlash('success', "Facture $id/20 a été supprimer avec succée");

        $entityManager->remove($facture);
        $entityManager->flush();

        return $this->redirectToRoute('factures');
    }

    /**
     * @Route("/factures/edit/{id}", name="factures.edit")
     */
    public function editFacture($id, Request $request, EntityManagerInterface $manager)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $facture = $entityManager->getRepository(Factures::class)->find($id);

        $repo = $this->getDoctrine()->getRepository(Factures::class);
        $factures = $repo->findAll();

        $form = $this->createForm(FacturesType::class, $facture);
        $form->remove('id');
        $form->remove('client');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', "Facture $id/20 a été modifier avec succée");
            return $this->redirectToRoute('factures');
        }
        return $this->render('admin/Edit_Factures.html.twig', [
            "formFacturesEdit" => $form->createView(),
            "i" => '2',
            "factures" => $factures,
            "id" => $id
        ]);

    }

    /**
     * @Route("/factures/ajouter", name="facture.ajouter")
     */
    public function FactureAjouter(Request $request, EntityManagerInterface $manager)
    {
        $facture = new Factures();
        $form = $this->createForm(FacturesType::class, $facture);
        $form->remove('id');
        $form->remove('date');
        $facture->setDate(new \DateTime());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($facture);
            $manager->flush();
            $this->addFlash('success', "Facture a été ajouter avec succée");
            return $this->redirectToRoute('factures');
        }

        return $this->render('admin/Ajouter_Factures.html.twig', [
            "formFactures" => $form->createView(), "i" => '2'
        ]);
    }

}
