<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/" )
     */
    public function main(){
        return $this->redirectToRoute('acceuil');
    }


    /**
     * @Route("/inscription", name="inscription")
     */

    public function login(Request $request , EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder,ClientRepository $repository)
    {
        if($this->getUser()==true){
            return $this->redirectToRoute('acceuil');
        }
        $user = new Client();

        $form = $this->createForm(ClientType::class,$user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if ($client=$repository->findOneBy(['telephone'=>$user->getTelephone()])){
                if ($client->getEmail()!=null){
                    $this->addFlash('error','Numero du Telephone Deja utilisé');
                    return $this->redirectToRoute('inscription');
                }
                if (!($client->getCin()==$user->getCin())){
                    $this->addFlash('error','Numero du CIN Deja utilisé');
                    return $this->redirectToRoute('inscription');
                }
                $client->setCin($user->getCin());
                $client->setEmail($user->getEmail());
                $client->setNom($user->getNom());
                $hash = $encoder->encodePassword($user , $user->getPassword());
                $client->setPassword($hash);
                $client->setPrenom($user->getPrenom());
                $manager->persist($client);
                $manager->flush();
            }else{
                $hash = $encoder->encodePassword($user , $user->getPassword());
                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();
            }
            return $this->redirectToRoute('connexion');
        }


        return $this->render('Client/Inscription.html.twig', [
            'formUser'=> $form->createView(),
        ]);
    }


    /**
     * @Route("/connexion" , name="connexion")
     */

    public  function connecter(AuthenticationUtils $authenticationUtils){

   if($this->getUser() == true)
   {
       return $this->redirectToRoute('acceuil');
   }
        $error=$authenticationUtils->getLastAuthenticationError();

        return $this->render('Client/connexion.html.twig',[
            'error'=>$error,
        ]);
    }

//    /**
//     * @Route("/connexion1" , name="connexion.admin")
//     */
//
//    public  function connecterAdmin(){
//
//
//        return $this->render('admin/connexion.html.twig');
//    }

    /**
     * @Route("/deconnexion" , name="deconnexion")
     */

    public  function deconnecter(){

    }

   /**
    * @Route("/profil/modifier/{id}" , name="modifier.profil")
    */

   public function modifier(Request $request , Client $client = null, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager){

       if($client == null )
       {
           return $this->redirectToRoute('inscription');
       }else{


           $form = $this->createForm(ClientType::class,$client);
           $form->remove('email');
           $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid())
           {
               $hash = $encoder->encodePassword($client , $client->getPassword());
               $client->setPassword($hash);
               $manager->persist($client);
               $manager->flush();
               return $this->redirectToRoute('acceuil');
           }
       }


       return $this->render('Client/EditAccount.html.twig' , [
           'formUser'=> $form->createView()
       ]);

   }



}
