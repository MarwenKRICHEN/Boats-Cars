<?php

namespace App\Controller;

use App\Entity\Contenir;
use App\Entity\Devis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DevisController
 * @package App\Controller
 * @Route ("/admin")
 */
class DevisController extends AbstractController
{
    /**
     * @Route("/devis", name="devis")
     */
    public function Devis(Request $request)
    {

        $repo = $this->getDoctrine()->getRepository(Devis::class);
        $header=$request->query->get('HEADER');
        $orderby=$request->query->get('ORDERBY');
        $mode=$request->query->get('MODE');
        if(!$mode){
            $header='Plus Recent';
            $orderby='date';
            $mode='DESC';
        }
        $headers=['Plus Recent'=>['date','DESC'],'Plus Ancient'=>['date','ASC'],'Non lus'=>['seen','ASC'],'lus'=>['seen','DESC'],'Non Repondus'=>['done','ASC'],'Repondus'=>['done','DESC']];
        $values = $repo->findBy([],[$orderby=>$mode]);
        return $this->render('admin/Devis.html.twig', [
            'i' => '4',
            "devises" => $values,
            'headers'=>$headers,
            'orderby'=>$orderby,
            'mode'=>$mode,
            'header'=>$header,
        ]);
    }


    /**
     * @Route("/devis/done/{id}", name="devis.done")
     */
    public function devisDone(Devis $devis = null,EntityManagerInterface $manager)
    {

        if($devis)
        {
            $devis->setDone(!$devis->getDone());
            $manager->persist($devis);
            $manager->flush();
            return $this->json(["succee"=>'done'],200);
        }
        return $this->redirectToRoute('devis');

    }

    /**
     * @Route("/devis/details/{id}", name="devis.detail")
     */

    public function devisDet(Devis $devis = null,EntityManagerInterface $manager)
    {
        $contenus = $manager->getRepository(Contenir::class)->findBy([
            'devis'=>$devis
        ]);
        if (!$devis->getSeen())
        {
            $devis->setSeen(1);
            $manager->persist($devis);
            $manager->flush();

        }

        return $this->render('admin/DevisDetail.html.twig', [
            'i' => '4',
            'contenus'=>$contenus

        ]);
    }

    /**
     * @Route("/devis/cal", name="devis.cal")
     */

    public function devisCal(EntityManagerInterface $manager)
    {
        $repo=$manager->getRepository(Devis::class);
        $num=$repo->findBy(["seen"=>false]);
        $num=count($num);
        return $this->json(['msg'=>$num],200);
    }


}
