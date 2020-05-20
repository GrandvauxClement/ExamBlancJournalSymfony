<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $userConnect = $this->getUser();
        if($userConnect == null){
            return $this->redirectToRoute('app_login');
        }
        else {
            $articles = $this->getDoctrine()->getRepository(Articles::class)->findAll();
            return $this->render('admin/index.html.twig', [
                'articles' => $articles,
                'user'=> $userConnect,
            ]);
        }

    }

    /**
     * @Route("/admin-addArticle", name="addArticle")
     */

    public function add(Request $request, SluggerInterface $slugger)
    {
        $userConnect = $this->getUser();
        $article = new Articles();
        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){


            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setImage($newFilename);
            }


            $article->setDateAjout(new \DateTime());
            $article->setJournaliste($userConnect->getNom());
            $article = $form->getData();

            $entityManager= $this->getDoctrine()->getManager();

            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('admin');
        } else {
            return $this->render('admin/add.html.twig',
                [
                    'form' => $form->createView(),
                    'user' => $userConnect,
                ]);
        }
    }

    /**
     * @Route("/admin-ArticleDelete/{article}", name="deleteArticle")
     */
    public function delete(Articles $article, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute('admin');

    }

    /**
     * @Route("/admin-updateArticle/{article}", name="updateArticle")
     */

    public function update(Request $request, SluggerInterface $slugger, Articles $article)
    {
        $userConnect = $this->getUser();
        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){


            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setImage($newFilename);
            }



            $article = $form->getData();
            $entityManager= $this->getDoctrine()->getManager();

            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('admin');
        } else {
            return $this->render('admin/update.html.twig',
                [
                    'form' => $form->createView(),
                    'article' => $article,
                    'user' => $userConnect,
                ]);
        }
    }
}
