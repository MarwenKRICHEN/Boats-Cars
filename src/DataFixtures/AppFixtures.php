<?php

namespace App\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Client;
use App\Entity\Bateaux;
use App\Entity\Admin;
use App\Entity\Contenir;
use App\Entity\Devis;
use App\Entity\Moteur;
use App\Entity\Produit;
use App\Entity\RendezVous;
use App\Entity\Factures;
use Faker\Factory;
use Yaf\Response\Cli;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $clients=array();
        $faker=\Faker\Factory::create('fr_FR');
        for($i=0;$i<20;$i++){
            $client=new Client();
            $client->setCin($faker->randomNumber(8))
                    ->setEmail($faker->email)
                    ->setNom($faker->name)
                    ->setPrenom($faker->firstName)
                    ->setPassword($faker->password)
                    ->setAvatar($faker->imageUrl(200,200))
                    ->setTelephone($faker->randomNumber(8));
            $clients[$i]=$client;
            $manager->persist($client);
        }
        $manager->flush();
        $repository =$manager->getRepository(Client::class);
        for($i=0;$i<10;$i++){
            $bateau=new Bateaux();
            try {
                $bateau->setNom($faker->name)
                    ->setMetrage($faker->randomFloat(3, 0, 15))
                    ->setMatricule($faker->bothify('??####'))
                    ->setOwner($clients[random_int(0,18)]);
            } catch (\Exception $e) {
                dump("3aslema");
            }
            $manager->persist($bateau);
        }
        $manager->flush();
        for($i=0;$i<5;$i++){
            $moteur=new Moteur();
            try {
               $moteur->setMarque($faker->company)
                        ->setNBCheveaux($faker->randomNumber(3))
                        ->setNumSeries($faker->bothify('?###??##'))
                        ->setOwner($clients[random_int(0,18)]);
            } catch (\Exception $e) {
                dump("3aslema");
            }
            $manager->persist($moteur);
        }
        $manager->flush();
        for($i=0;$i<5;$i++){
            $factures=new Factures();
            try {
                $factures->setDate(\DateTime::createFromFormat('Y-m-d', $faker->date('Y-m-d')))
                        ->setMontant($faker->randomFloat(3))
                        ->setClient($clients[random_int(0,18)]);
            } catch (\Exception $e) {
                dump("3aslema");
            }
            $manager->persist($factures);
        }
        $manager->flush();
        for($i=0;$i<5;$i++){
            $rendezv=new RendezVous();
            try {
                $rendezv->setDate(\DateTime::createFromFormat('Y-m-d', $faker->date('Y-m-d')))
                        ->setHeure(\DateTime::createFromFormat('H:i', $faker->date('H:i')))
                        ->setClient($clients[random_int(0,18)]);
            } catch (\Exception $e) {
                dump("3aslema");
            }
            $manager->persist($rendezv);
        }
        $manager->flush();
        $arrayValues = ['low', 'medium', 'hight'];
        $prods=[];
        for($i=0;$i<20;$i++){
            $prod=new Produit();
            $prod->setNom($faker->name)
                ->setCat($arrayValues[rand(0,2)])
                ->setDisponible($faker->boolean)
                ->setNeuf($faker->boolean)
                ->setPrix($faker->randomFloat(3))
                ->setRef($faker->bothify('?###??##'));
            $manager->persist($prod);
            $prods[$i]=$prod;
        }
        $manager->flush();
        $deviss=[];
        for($i=0;$i<10;$i++){
            $devis=new Devis();
            try {
                $devis->setDate(\DateTime::createFromFormat('Y-m-d', $faker->date('Y-m-d')))
                    ->setDone($faker->boolean)
                    ->setClient($clients[random_int(0,18)]);
            } catch (\Exception $e) {
            }
            $manager->persist($devis);
            $deviss[$i]=$devis;
        }
        $manager->flush();
        for($i=0;$i<10;$i++){
            $contenir=new Contenir();
            try {
                $contenir->setDevis($deviss[random_int(0,8)]);
                $contenir->setProduit($prods[random_int(0,18)]);
            } catch (\Exception $e) {
            }
            $contenir->setQuantite($faker->randomNumber(2));
            $manager->persist($contenir);
        }
        $manager->flush();
    }
}
