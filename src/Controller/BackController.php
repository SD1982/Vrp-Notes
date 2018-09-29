<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BackController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function adminDashboard()
    {
        return $this->render('back/dashboard.html.twig');
    }

    /**
     * @Route("/admin/note/listing", name="admin_note_listing")
     */
    public function notelisting()
    {
        $repo = $this->getDoctrine()->getRepository(Note::class);

        $notes = $repo->findAll();

        return $this->render('front/notes_listing.html.twig', [
            'notes' => $notes
        ]);
    }

    /**
     * @Route("/admin/note/{id}", name="admin_note_show")
     */
    public function noteShow()
    {
        $repo = $this->getDoctrine()->getRepository(Note::class);


        return $this->render('front/note.html.twig');
    }
}
