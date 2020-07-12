<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Produit;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;

/**
 * Class GalleryController
 * @package App\Controller
 * @Route ("/admin")
 */

class GalleryController extends AbstractController
{

    /**
     * @Route("/" ,name="home2")
     */
    public function home2(){

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/home", name="home")
     */

    public function home(ClientRepository $repository)
    {
        return $this->render('admin/Home.html.twig');
      
    }

    /**
     * @Route("/galerie", name="galerie")
     */
    public function galerie()
    {
        $finder = new Finder();
        $finder->files()->in($this->getParameter('4x4_images'));

        $im4 = count($finder);
        $finder1 = new Finder();
        $finder1->files()->in($this->getParameter('bateaux_images'));

        $imBateaux = count($finder1);
        return $this->render('admin/Galeries.html.twig', [
                "i" => '6',
                'im4'=> $im4,
                'imB'=> $imBateaux
            ]

        );
    }

    /**
     * @Route("/galerie/4x4", name="galerie.4x4")
     */
    public function galerie4x4()
    {
        $finder = new Finder();
        $finder->files()->in($this->getParameter('4x4_images'));
        $filesnames=[];
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $fileNameWithExtension = $file->getRelativePathname();
                array_push($filesnames,$fileNameWithExtension);
            }
        }
        return $this->render('admin/Galerie-4x4.html.twig', [
                "i" => '6',
                "imgs"=>$filesnames,
            ]

        );
    }

    /**
     * @Route("/galerie/bateaux", name="galerie.bateaux")
     */
    public function galerieBateaux()
    {
        $finder = new Finder();
        $finder->files()->in($this->getParameter('bateaux_images'));
        $filesnames=[];
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $fileNameWithExtension = $file->getRelativePathname();
                array_push($filesnames,$fileNameWithExtension);
            }
        }
        return $this->render('admin/Galerie-Bateaux.html.twig', [
                "i" => '6',
                "imgs"=>$filesnames,
            ]

        );
    }
    /**
     * @Route("/galerie/bateaux/delete/{name}", name="image.delete")
     */
    public function galerieBateauxDelete($name)
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getParameter('bateaux_images').'/'.$name);
        return $this->json(['img' => 'done'], 200);
    }

    /**
     * @Route("/galerie/bateaux/add", name="image.upload")
     */
    public function imageuploader(Request $request,EntityManagerInterface $manager)
    {
        if ($request->isXmlHttpRequest()) {
            $image = $request->files->get('files');
            $imageName = md5(uniqid()) . '.' . $image->guessExtension();
            $image->move($this->getParameter('bateaux_images'), $imageName);
            return $this->json(['img' => $imageName], 200);
        }
    }

    /**
     * @Route("/galerie/4x4/delete/{name}", name="image4x4.delete")
     */
    public function galerie4x4Delete($name)
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getParameter('4x4_images').'/'.$name);
        return $this->json(['img' => 'done'], 200);
    }

    /**
     * @Route("/galerie/4x4/add", name="image4x4.upload")
     */
    public function image4x4uploader(Request $request,EntityManagerInterface $manager)
    {
        if ($request->isXmlHttpRequest()) {
            $image = $request->files->get('files');
            $imageName = md5(uniqid()) . '.' . $image->guessExtension();
            $image->move($this->getParameter('4x4_images'), $imageName);
            return $this->json(['img' => $imageName], 200);
        }
    }




}

