<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @Route("/user/contact-admin", name="user_contact_admin")
     * @Route("/user/message-response", name="user_message_response")
     * @Route("/admin/contact-user", name="admin_contact_user")
     * @Route("/admin/message-response", name="admin_message_response")
     * @Route("/admin/note-message", name="admin_note_message")
     */
    public function messageForm()
    {
        $form = $this->createForm(MessageType::class);

        return $this->render('message/message_form.html.twig');
    }
}
