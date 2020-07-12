<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProduitController
 * @package App\Controller
 * @Route ("/admin")
 */

class ProduitController extends AbstractController
{
    /**
     * @Route("/produits/{id}/images", name="images.edit")
     */

    public function ViewEdit (Produit $produit=null, EntityManagerInterface $manager, Request $request):Response
    {
            if ($produit){
                $im=$produit->getImages();
                $form=$this->createForm(ProduitType::class,$produit);
                $form->remove('nom');
                $form->remove('ref');
                $form->remove('cat');
                $form->remove('neuf');
                $form->remove('prix');
                $form->handleRequest($request);
                if ($form->isSubmitted()){
                    $image1 = $form->get('image1')->getData();
                    if($image1)
                    {
                        if($im[0])
                        {
                            $this->replaceImage($image1, $im[0], $manager);
                        }
                        else
                        {
                            $this->UploadImage($image1,$produit,$manager);
                        }
                    }
                    $image2 = $form->get('image2')->getData();
                    if($image2)
                    {
                        if($im[1])
                        {
                            $this->replaceImage($image2, $im[1], $manager);
                        }
                        else
                        {
                            $this->UploadImage($image2,$produit,$manager);
                        }
                    }
                    $image3 = $form->get('image3')->getData();
                    if($image3)
                    {
                        if($image3 && $im[2])
                        {
                            $this->replaceImage($image3, $im[2], $manager);
                        }
                        else
                        {
                            $this->UploadImage($image3,$produit,$manager);
                        }
                    }
                    return $this->redirectToRoute('produit');
                }
            }
            return $this->render("admin/Modals/ImagesModal.html.twig",[
                "form"=>$form->createView(),
                "images"=>$im,
                "id"=>$produit->getId()
                ]);
    }

    public function replaceImage ($image, $toreplace, EntityManagerInterface $manager)
    {
        $imageName = md5(uniqid()).'.'.$image->guessExtension();
        $todelete=$toreplace->getUrl();
        $filesystem = new Filesystem();
        $filesystem->remove($this->getParameter('produit_images').'/'.$todelete);
        $image->move( $this->getParameter('produit_images'),$imageName);
        $toreplace->setUrl($imageName);
        $manager->persist($toreplace);
        $manager->flush();
    }

    /**
     * @Route("/produits", name="produit")
     */
    public function produit(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Produit::class);
        $orderby=$request->query->get('ORDERBY');
        $mode=$request->query->get('MODE');
        if(!$mode){
            $orderby='id';
            $mode='ASC';
        }
        $headers=['Réfèrence'=>'ref','Disponibilité'=>'disponible','Nom'=>'nom','Catégorie'=>'cat','Prix'=>'prix','Etat'=>'neuf'];
        $values = $repo->findBy([],[$orderby=>$mode]);
        return $this->render('admin/Produit.html.twig',[
                "i"=>'3',
                'Produits'=>$values,
                'headers'=>$headers,
                'orderby'=>$orderby,
                'mode'=>$mode,
            ]
        );
    }

    public function UploadImage($image,Produit $Produit,EntityManagerInterface $manager )
    {
        $imageName = md5(uniqid()).'.'.$image->guessExtension();
        $image->move( $this->getParameter('produit_images'),$imageName);
        $imObject = $this->addImageDeProduit($imageName,$manager,$Produit);
        $Produit->addImage($imObject);
    }

    public function addImageDeProduit($imageName, EntityManagerInterface $manager, $Produit){

        $image = new Images();

        $image->setUrl($imageName);
        $image->setProduit($Produit);
        $manager->persist($image);
        $manager->flush();

        return $image;

    }

    /**
     * @Route("/produits/ajouter", name="produit.ajouter")
     * @Route("/produits/{id}/update" , name="produit.edit")
     */

    public function ProduitAjouter(Request $request, EntityManagerInterface $manager, Produit $Produit=null)
    {   $New=false;
        if(!$Produit)
        {
            $Produit = new Produit();
            $New=true;
        }

        $form=$this->createForm(ProduitType::class,$Produit);
        if (!$New){
            $form->remove('image1');
            $form->remove('image2');
            $form->remove('image3');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid()){

            $Produit->setDisponible(true);
            $manager->persist($Produit);
            $manager->flush();

            if ($New){
            $image1 = $form->get('image1')->getData();
            if($image1)
            {
                $this->UploadImage($image1,$Produit,$manager);
            }


            $image2 = $form->get('image2')->getData();
            if($image2)
            {
                $this->UploadImage($image2,$Produit,$manager);
            }

            $image3 = $form->get('image3')->getData();
            if($image3)
            {
                $this->UploadImage($image3,$Produit,$manager);
            }}
            if ($New){
                $this->addFlash('success',"le produit ".$Produit->getRef()." a été ajouter avec succés");
            }else{
                $this->addFlash('success',"le produit ".$Produit->getRef()." a été modifer avec succés");
            }
            return $this->redirectToRoute('produit');

        }

        return $this->render('admin/Ajouter_Produit.html.twig',[
            "formProduit"=>$form->createView(),
            "i"=>'3',
            "New"=>$New
        ]);

    }



    /**
     * @Route("/produits/dispo/{id}" , name="produit.dispo")
     */

    public function dispo($id){

        $manager = $this->getDoctrine()->getManager();

        $Produit = $manager->getRepository(Produit::class)->find($id);

        $Produit->setDisponible(! ($Produit->getDisponible())) ;
        $manager->flush();

        return $this->json(['message'=> 'modifié avec success ', 'dispo'=> $Produit->getDisponible() ], 200);


    }

    /**
     * @Route("/produits/delete/{id}" , name="produit_delete")
     */

    public function deleteProduit($id){

        $manager = $this->getDoctrine()->getManager();

        $Produit = $manager->getRepository(Produit::class)->find($id);

        $manager->remove($Produit);
        $manager->flush();

        return $this->redirectToRoute('produit');
    }

}
