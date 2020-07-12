<?php

namespace App\Controller;

use App\Entity\Bateaux;
use App\Entity\Client;
use App\Entity\Moteur;
use App\Form\BateauType;
use App\Form\ClientType;
use App\Form\MoteurType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DonneeController
 * @package App\Controller
 * @Route ("/admin")
 */
class DonneeController extends AbstractController
{
    //Bateau

    /**
     * @Route("/donnee/bateaux", name="bateaux")
     */
    public function Bateaux(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Bateaux::class);

        $orderby=$request->query->get('ORDERBY');
        $mode=$request->query->get('MODE');
        if(!$mode){
            $orderby='id';
            $mode='ASC';
        }
        $headers=['Matricule'=>'matricule','Nom'=>'nom','Métrage'=>'metrage','Télephone du Client'=>'owner.telephone'];
        if($orderby=='owner.telephone'){
            $values=$repo->orderByClient($orderby,$mode);
        }else{
            $values = $repo->findBy([],[$orderby=>$mode]);
        }
        return $this->render('admin/Bateaux.html.twig', [
            'i' => '1',
            'Bateaux'=> $values,
            'headers'=>$headers,
            'orderby'=>$orderby,
            'mode'=>$mode,
        ]);
    }


    /**
     * @Route("/donnee/bateaux/ajouter", name="bateau.ajouter")
     */
    public function BateauAjouter(Request $request, EntityManagerInterface $manager)
    {
        $bateau = new Bateaux();

        $form=$this->createForm(BateauType::class,$bateau);

        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid()){

            dump($bateau);
            $manager->persist($bateau);
            $manager->flush();
            $this->addFlash('success',$bateau->getNom()." a été ajouter avec succée");
            return $this->redirectToRoute('bateaux');
        }
        return $this->render('admin/Ajouter_Bateaux.html.twig',[
            "formBateau"=>$form->createView(),
            "i"=>'1'
        ]);

    }

    /**
     * @Route("/donnee/bateaux/delete/{id}", name="bateau.delete")
     */
    public function Bateaudelete(Bateaux $bateau = null, EntityManagerInterface $manager)
    {
        if($bateau)
        {
            $manager->remove($bateau);
            $manager->flush();

        }
        return $this->redirectToRoute('bateaux');
    }

    //Moteur

    /**
     * @Route("/donnee/moteurs", name="moteur")
     */
    public function Moteurs(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Moteur::class);

        $orderby=$request->query->get('ORDERBY');
        $mode=$request->query->get('MODE');
        if(!$mode){
            $orderby='id';
            $mode='ASC';
        }
        $headers=['Numero de Serie'=>'num_series','Marque'=>'marque','Nombre de Cheveaux'=>'nBCheveaux','Télephone du Client'=>'owner.telephone'];
        if($orderby=='owner.telephone'){
            $values=$repo->orderByClient($orderby,$mode);
        }else{
            $values = $repo->findBy([],[$orderby=>$mode]);
        }

        return $this->render('admin/Moteurs.html.twig', [
            'i' => '1',
            'Moteurs'=> $values,
            'headers'=>$headers,
            'orderby'=>$orderby,
            'mode'=>$mode,
        ]);
    }


    /**
     * @Route("/donnee/moteur/ajouter", name="moteur.ajouter")
     */
    public function MoteurAjouter(Request $request, EntityManagerInterface $manager)
    {

        $moteur = new Moteur();
        $form = $this->createForm(MoteurType::class, $moteur);
        $form->handleRequest($request);
        dump($form->isSubmitted());
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($moteur);
            $manager->flush();
            $this->addFlash('success', " Le moteur de " . $moteur->getOwner()->getNom() . " " . $moteur->getOwner()->getPrenom() . " a été ajouter avec succée");
            return $this->redirectToRoute('moteur');
        }
        return $this->render('admin/Ajouter_Moteur.html.twig', [
            "formMoteur" => $form->createView(), "i" => '1'
        ]);
    }

    /**
     * @Route("/donnee/moteurs/delete/{id}", name="moteur.delete")
     */
    public function moteurdelete(Moteur $moteur = null, EntityManagerInterface $manager)
    {
        if($moteur)
        {
            $manager->remove($moteur);
            $manager->flush();

        }
        return $this->redirectToRoute('moteur');
    }

    //Client

    /**
     * @Route("/donnee/client", name="client")
     */
    public function Client(Request $request)
    {
        $orderby=$request->query->get('ORDERBY');
        $mode=$request->query->get('MODE');
        if(!$mode){
            $orderby='id';
            $mode='ASC';
        }
        $headers=['CIN'=>'cin','Nom'=>'nom','Prenom'=>'prenom','E-mail'=>'email','Telephone'=>'telephone'];
        $values=$this->getDoctrine()->getRepository(Client::class)->findBy(['role'=>'0'],[$orderby=>$mode]);
        return $this->render('admin/Client.html.twig', [
            'i' => '1',
            'values'=>$values,
            'headers'=>$headers,
            'orderby'=>$orderby,
            'mode'=>$mode,
        ]);
    }

    /**
     * @Route("/donnee", name="donnee")
     */
    public function Donnee()
    {
        return $this->redirectToRoute('client');
    } //whattttt?


    /**
     * @Route("/donnee/client/ajouter", name="client.ajouter")
     */
    public function ClientAjouter(Request $request, EntityManagerInterface $manager,ClientRepository $repository)
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->remove('email');
        $form->remove('avatar');
        $form->remove('password');
        $form->remove('confirm_password');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user=$repository->findOneBy(['cin'=>$client->getCin()]);
            if ($user!=null){
                $this->addFlash('error','Numero du CIN Deja utilisé');
            }
            $user1=$repository->findOneBy(['telephone'=>$client->getTelephone()]);
            if ($user1!=null){
                $this->addFlash('error','Numero du Telephone Deja utilisé');
            }
            if($user!=null or $user1!=null){
                return $this->redirectToRoute('client.ajouter');
            }
            $manager->persist($client);
            $manager->flush();
            $this->addFlash('success', $client->getNom() . " " . $client->getPrenom() . " a été ajouter avec succée");
            return $this->redirectToRoute('client');
        }
        return $this->render('admin/Ajouter_Client.html.twig', [
            "formClient" => $form->createView(), "i" => '1'
        ]);

    }
}
