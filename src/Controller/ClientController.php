<?php

namespace App\Controller;

use App\Entity\Contenir;
use App\Entity\Devis;
use App\Entity\Produit;
use App\Repository\ClientRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    /**
     * @Route("/acceuil", name="acceuil")
     */
    public function index()
    {

        return $this->render('Client/Acceuil.html.twig');
    }

    /**
     * @Route("/services" , name="services")
     */
    public function services(){

        return $this->render('Client/Services.html.twig');
    }



    /**
     * @Route("/services/gallerie" , name="gallerie.bateaux")
     */

    public function gal(){
        $finder = new Finder();
        $finder->files()->in($this->getParameter('bateaux_images'));
        $filesnames=[];
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $fileNameWithExtension = $file->getRelativePathname();
                array_push($filesnames,$fileNameWithExtension);
            }
        }
        return $this->render('Client/Gallerie_bateaux.html.twig',[
            "pics"=>$filesnames,
        ]);
    }

    /**
     * @Route("/VehiculeTousTerrain" , name="gallerie.4x4")
     */

    public function galerie(){
        $finder = new Finder();
        $finder->files()->in($this->getParameter('4x4_images'));
        $filesnames=[];
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $fileNameWithExtension = $file->getRelativePathname();
                array_push($filesnames,$fileNameWithExtension);
            }
        }
        return $this->render('Client/Gallerie_4x4.html.twig',[
            "pics"=>$filesnames,
        ]);
    }


    /**
     * @Route("/accessoires" , name="accessoires")
     */
    public function accessoires(Request $request,EntityManagerInterface $manager){
        $cat=$request->query->get('cat');
        $filtre=$request->query->get('filtre');
        $trier=$request->query->get('trier');
        if(!$cat){
            $cat='Catégorie';
        }
        if(!$filtre){
            $filtre='Filtre';
        }
        if(!$trier){
            $trier='Trier';
        }
        $cats=['Catégorie'=>null,'Alternateurs'=>'Alternateurs','Accessoires marin'=>'Accessoires marin','Boite de vitesse marine'=>'Boite de vitesse marine','Commande et direction'=>'Commande et direction'];
        $filtres=['Filtre'=>null,'Neuf'=>1,'Occasion'=>0];
        $triers=['Trier'=>null,'A-Z'=>'nom','Catégorie'=>'cat'];
        $criteria = array_filter(array(
            'cat' => $cats[$cat],
            'neuf' => $filtres[$filtre],
        ));
        $repo = $this->getDoctrine()->getRepository(Produit::class);
        if($trier=='Trier'){
            $orderby='id';
        }else{
            $orderby=$triers[$trier];
        }
        if($filtre!='Occasion')
            $prod=$repo->findBy($criteria,[$orderby=>'ASC']);
        else
            $prod=$repo->getWhatYouWant($cats[$cat],$orderby);
        return $this->render('Client/Accessoires.html.twig',[
            'prod'=>$prod,
            'cats'=>$cats,
            'filtres'=>$filtres,
            'triers'=>$triers,
            'cat'=>$cat,
            'filtre'=>$filtre,
            'trier'=>$trier,
        ]);

    }
    /**
     * @Route("/panier",name="panier")
     */
    public function panier(SessionInterface $session,ProduitRepository $repository)
    {
        $panier = $session->get('panier',[]);
        $products=[];
        foreach ($panier as $id=>$qte){
            $products[]=["product"=>$repository->find($id),"qte"=>$qte];
        }
        return $this->render('Client/Panier.html.twig',[
            "products"=>$products,
        ]);
    }

    /**
     * @Route("/panier/delete/{id}",name="panier.delete")
     */
    public function panierDelete(SessionInterface $session,$id)
    {
        $panier = $session->get('panier',[]);
        unset($panier[$id]);
        $session->set('panier',$panier);
        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/panier/envoyer",name="panier.envoyer")
     */
    public function panierEnvoyer(SessionInterface $session,ClientRepository $repository,ProduitRepository $repository1,EntityManagerInterface $manager)
    {
        $user=$repository->findOneBy(['email'=>$this->getUser()->getUsername()]);
        $panier = $session->get('panier',[]);
        $session->remove('panier');
        $this->addFlash('success','Votre demande de devis a été envoyer avec succés');
        $devis=new Devis();
        $devis->setDate(new \DateTime());
        $devis->setClient($user);
        $manager->persist($devis);
        $manager->flush();
        foreach ($panier as $id=>$qte){
            $cont=new Contenir();
            $cont->setDevis($devis);
            $cont->setProduit($repository1->find($id));
            $cont->setQuantite($qte);
            $manager->persist($cont);
        }
        $manager->flush();

        return $this->redirectToRoute('panier');
    }


    /**
     * @Route("/accessoires/ajouter/{id}/{qt?1}",name="accessoire.ajouter")
     */
    public function ajouterProd($id,$qt,SessionInterface $session)
    {
        $panier = $session->get('panier',[]);
        if(empty($panier[$id])){
            $panier[$id]=0;
        }
        $panier[$id]+=$qt;
        $session->set('panier',$panier);
//        dd($session->get('panier'));
        return $this->json(['msg'=>'done'],200);
    }

    /**
     * @Route("/accessoires/modifier/{id}/{qt?1}",name="accessoire.modifier")
     */
    public function modifierProd($id,$qt,SessionInterface $session)
    {
        $panier = $session->get('panier',[]);
        $panier[$id]=$qt;
        $session->set('panier',$panier);
//        dd($session->get('panier'));
        return $this->json(['msg'=>'done'],200);
    }

    /**
     * @Route("/accessoires/{id}/details" , name="details.article")
     */
    public function detailsProd(Produit $produit){

       return $this->render('Client/Modals/ProduitDetails.html.twig', [
            'produit'=> $produit,
        ]);
    }

    /**
     * @Route("/services/article/{id}" , name="article")
     */

    public function article($id)
    {

        return $this->render('Client/Article.html.twig', [
            'id'=> $id
        ]);
    }
}
