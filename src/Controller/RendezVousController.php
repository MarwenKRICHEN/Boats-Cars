<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RendezVousController
 * @package App\Controller
 * @Route ("/admin")
 */


class RendezVousController extends AbstractController
{


    /**
     * @Route("/rendez-vous", name="rendez-vous")
     */
    public function rendezvous(Request $request)
    {
        $orderby=$request->query->get('ORDERBY');
        $mode=$request->query->get('MODE');
        if(!$mode){
            $orderby='id';
            $mode='ASC';
        }
        $headers=['Nom Client'=>'nom','Date'=>'date','Heure'=>'heure','Num Telephone'=>'telephone'];
        $repo = $this->getDoctrine()->getRepository(RendezVous::class);
        if($orderby=='nom' or $orderby=='telephone'){
            $values=$repo->orderByClient($orderby,$mode);
        }else{
            $values = $repo->findBy([],[$orderby=>$mode]);
        }
//        dd($values);
        return $this->render('admin/Rendez-vous.html.twig', [
                "i" => '5',
                "values" => $values,
                "rendezvous"=>null,
                "headers"=>$headers,
                "orderby"=>$orderby,
                "mode"=>$mode,
            ]
        );
    }

    /**
     * @Route("/rendez-vous/edit/{id}", name="rendez-vous.edit")
     */
    public function rendezvousedit(EntityManagerInterface $manager,Request $request,RendezVous $rendezVous=null)
    {
        dump($rendezVous);
        $form=$this->createForm(RendezVousType::class,$rendezVous);
        $form->remove('client');
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $manager->persist($rendezVous);
            $manager->flush();
            $this->addFlash('success','RendezVous modifier avec succée');
            return $this->redirectToRoute('rendez-vous');
        }
        $orderby=$request->query->get('ORDERBY');
        $mode=$request->query->get('MODE');
        if(!$mode){
            $orderby='id';
            $mode='ASC';
        }
        $headers=['Nom Client'=>'nom','Date'=>'date','Heure'=>'heure','Num Telephone'=>'telephone'];
        $repo = $this->getDoctrine()->getRepository(RendezVous::class);
        if($orderby=='nom' or $orderby=='telephone'){
            $values=$repo->orderByClient($orderby,$mode);
        }else{
            $values = $repo->findBy([],[$orderby=>$mode]);
        }
        return $this->render('admin/Rendez-vous.html.twig', [
                "i" => '5',
                "values" => $values,
                "rendezvous"=>$rendezVous,
                "headers"=>$headers,
                "orderby"=>$orderby,
                "mode"=>$mode,
                "form"=>$form->createView(),
            ]
        );
    }


    /**
     * @Route("/rendez-vous/delete/{id}", name="rendez-vous.delete")
     */
    public function RendezVousDelete(EntityManagerInterface $manager, RendezVous $rendezVous = null)
    {
        if($rendezVous)
        {
            $manager->remove($rendezVous);
            $manager->flush();
            $this->addFlash('success', "Rendez-Vous avec ".$rendezVous->getClient()->getNom()." supprimée avec succée");
        }
        return $this->redirectToRoute('rendez-vous');
    }

    /**
     * @Route("/rendez-vous/ajouter", name="rendez-vous.ajouter")
     */

    public function RendezVousAjouter(Request $request, EntityManagerInterface $manager)
    {
        $rend = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rend);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($rend);
            $manager->flush();
            $this->addFlash('success', "le rendez vous a été ajouter avec succée");
            return $this->redirectToRoute('rendez-vous');
        }
        return $this->render('admin/Ajouter_Rendez-vous.html.twig', [
            "formRendez" => $form->createView(), "i" => '5'
        ]);

    }
}
