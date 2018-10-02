<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /** 
     * @Route("/user", name="user_dashboard")
     */
    public function userDashboard()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Note::class);

        $notes = $repo->findByUser($user);

        return $this->render('user/dashboard.html.twig', [
            'notes' => $notes,
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/note/new", name="user_new_note")
     * @Route("/admin/note/{id}/edit", name="admin_note_edit")
     */
    public function noteGestion(Note $note = null, Request $request, ObjectManager $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$note) {
            $note = new Note();
        }

        $form = $this->createForm(NoteType::class, $note);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$note->getId()) {
                $note->setCreatedAt(new \DateTime())
                    ->setStatut('En cours')
                    ->setUser($user);
            }

            $manager->persist($note);
            $manager->flush();

            return $this->redirectToRoute('user_note_show', ['id' => $note->getId()]);
        }

        return $this->render('user/note_registration_form.html.twig', [
            'form' => $form->createView(),
            'editMode' => $note->getId() !== null
        ]);
    }

    /**
     * @Route("/user/note/listing", name="user_note_listing")
     */
    public function notelisting()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Note::class);

        $notes = $repo->findByUser($user);

        return $this->render('user/notes_listing.html.twig', [
            'notes' => $notes
        ]);
    }

    /**
     * @Route("/user/note/{id}", name="user_note_show")
     */
    public function noteShow($id)
    {
        $repo = $this->getDoctrine()->getRepository(Note::class);

        $note = $repo->find($id);

        return $this->render('user/note.html.twig', [
            'note' => $note
        ]);
    }


}
