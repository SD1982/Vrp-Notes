<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="message")
     */
    public function message(Message $message = null, Request $request, ObjectManager $manager)
    {
        $message = new Message();

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setCreatedAt(new \DateTime());
            $message->setAuteur($user);
            $message->setStatut('Non lu');

            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('home');

        }

        return $this->render('message/message_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**   
     * @Route("/message/{id}/delete", name="message_delete")
     */
    public function messageDelete($id, ObjectManager $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $message = $entityManager->getRepository(Message::class)->find($id);

        $entityManager->remove($message);
        $entityManager->flush();

        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();

        $repo = $this->getDoctrine()->getRepository(Message::class);
        $messagesNonLus = $repo->findBy([
            "destinataire" => $user,
            "statut" => 'Non lu'
        ]);

        // On récupère la liste des rôles d'un utilisateur
        $roles = $user->getRoles();
        // On transforme le tableau d'instance en tableau simple
        $rolesTab = array_map(function ($role) {
            return $role;
        }, $roles);
        // Si admin redirection vers le home admin
        if (in_array('ROLE_ADMIN', $rolesTab, true)) {
            return $this->redirectToRoute('admin_home');
            return $this->render('admin/home.html.twig', [
                'user' => $user,
                'messagesNonLus' => $messagesNonLus,
                'articles' => $articles
            ]);
        // Si user redirection vers le home user
        } elseif (in_array('ROLE_USER', $rolesTab, true)) {
            return $this->redirectToRoute('user_home');
            return $this->render('user/home.html.twig', [
                'user' => $user,
                'messagesNonLus' => $messagesNonLus,
                'articles' => $articles
            ]);
        }
    }

    /**   
     * @Route("/message/{id}/change/statut", name="message_change_statut")
     */
    public function messageChangeStatut($id, ObjectManager $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $message = $entityManager->getRepository(Message::class)->find($id);

        $message->setStatut('Lu');
        $manager->persist($message);
        $manager->flush();

        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();

        $repo = $this->getDoctrine()->getRepository(Message::class);
        $messagesNonLus = $repo->findBy([
            "destinataire" => $user,
            "statut" => 'Non lu'
        ]);

        // On récupère la liste des rôles d'un utilisateur
        $roles = $user->getRoles();
        // On transforme le tableau d'instance en tableau simple
        $rolesTab = array_map(function ($role) {
            return $role;
        }, $roles);
        // Si admin redirection vers le home admin
        if (in_array('ROLE_ADMIN', $rolesTab, true)) {
            return $this->redirectToRoute('admin_home');
            return $this->render('admin/home.html.twig', [
                'user' => $user,
                'messagesNonLus' => $messagesNonLus,
                'articles' => $articles
            ]);
        // Si user redirection vers le home user
        } elseif (in_array('ROLE_USER', $rolesTab, true)) {
            return $this->redirectToRoute('user_home');
            return $this->render('user/home.html.twig', [
                'user' => $user,
                'messagesNonLus' => $messagesNonLus,
                'articles' => $articles
            ]);
        }
    }

}
