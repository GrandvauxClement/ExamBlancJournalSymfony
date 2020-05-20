<?php

namespace App\Controller;

use App\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="articles")
     */
    public function index()
    {
        $userConnect = $this->getUser();
        $articles = $this->getDoctrine()->getRepository(Articles::class)->findAll();
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
            'user' => $userConnect,
        ]);
    }

    /**
     * @Route("/detail-article/{article}", name="detail")
     */
    public function detail(Articles $article)
    {
        $userConnect = $this->getUser();
        return $this->render('articles/detail.html.twig', [
            'article' => $article,
            'user' => $userConnect,
        ]);
    }


}
