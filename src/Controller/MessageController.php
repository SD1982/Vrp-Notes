<?php

namespace App\Controller;

use App\Entity\User;
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

}
