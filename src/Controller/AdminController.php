<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function adminDashboard()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Note::class);
        $notes = $repo->findAll();

        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'notes' => $notes,
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/user/listing", name="admin_user_listing")
     */
    public function userlisting()
    {
        $repo = $this->getDoctrine()->getRepository(User::class);

        $users = $repo->findAll();

        return $this->render('admin/users_listing.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="admin_user_show")
     */
    public function userShow($id)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);

        $repo = $this->getDoctrine()->getRepository(Note::class);
        $notes = $repo->findByUser($id);

        return $this->render('admin/user.html.twig', [
            'user' => $user,
            'notes' => $notes
        ]);
    }
    /**
     * @Route("/admin/note/listing", name="admin_note_listing")
     */
    public function notelisting()
    {
        $repo = $this->getDoctrine()->getRepository(Note::class);

        $notes = $repo->findAll();

        return $this->render('admin/notes_listing.html.twig', [
            'notes' => $notes
        ]);
    }

    /**
     * @Route("/admin/note/{id}", name="admin_note_show")
     */
    public function noteShow($id)
    {
        $repo = $this->getDoctrine()->getRepository(Note::class);
        $note = $repo->find($id);

        return $this->render('admin/note.html.twig', [
            'note' => $note,
        ]);
    }

    /**
     * @Route("/admin/valid/note/{id}", name="admin_note_validation")
     */
    public function noteValidation($id, ObjectManager $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Note::class);
        $note = $repo->find($id);

        $note->setStatut('Validée');
        $manager->persist($note);
        $manager->flush();

        return $this->render('admin/note.html.twig', [
            'note' => $note,
        ]);
    }
}

