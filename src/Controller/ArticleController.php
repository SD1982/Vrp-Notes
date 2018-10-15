<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/add/article", name="article_add")
     * @Route("/admin/article/{id}/edit", name="article_edit")
     */
    public function articleGestion(Article $article = null, Request $request, ObjectManager $manager)
    {
        if (!$article) {
            $article = new Article();
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $auteur = $user->getName();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTime());
            $article->setAuteur($auteur);

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('home');

        }

        return $this->render('article/article_form.html.twig', [
            'form' => $form->createView(),
            'editMode' => $article->getId() !== null,
        ]);
    }
}
