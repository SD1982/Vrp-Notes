<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends Controller
{
    /** 
     * @Route("/admin/home", name="admin_home")
     */
    public function home()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('admin/home.html.twig', [
            'user' => $user
        ]);
    }

    /** 
     * @Route("/admin/validated", name="admin_validated_notes")
     */
    public function validatedNotes(Request $request)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Note::class);


        $validatedNotes = $repo->findBy([
            "statut" => 'Validée'
        ]);
            /* @var $paginator \Knp\Component\Pager\Paginator */
        $paginator = $this->get('knp_paginator');
        
             // Paginate the results of the query
        $validatedNotes = $paginator->paginate(
            // Doctrine Query, not results
            $validatedNotes,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            6
        );

        return $this->render('admin/validated_notes.html.twig', [
            'validated_notes' => $validatedNotes,
        ]);
    }

    /** 
     * @Route("/admin/waiting", name="admin_waiting_notes")
     */
    public function waitingNotes(Request $request)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Note::class);

        $notValidatedNotes = $repo->findBy([
            "statut" => 'En cours',
        ]);
         
            /* @var $paginator \Knp\Component\Pager\Paginator */
        $paginator = $this->get('knp_paginator');
        
             // Paginate the results of the query
        $notValidatedNotes = $paginator->paginate(
            // Doctrine Query, not results
            $notValidatedNotes,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            6
        );

        return $this->render('admin/waiting_notes.html.twig', [
            'not_validated_notes' => $notValidatedNotes,
        ]);
    }

    /** 
     * @Route("/admin/employee", name="admin_employee_listing")
     */
    public function vrp(Request $request)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        dump($users);
         
            /* @var $paginator \Knp\Component\Pager\Paginator */
        $paginator = $this->get('knp_paginator');
        
             // Paginate the results of the query
        $users = $paginator->paginate(
            // Doctrine Query, not results
            $users,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            6
        );

        return $this->render('admin/employee_listing.html.twig', [
            'users' => $users,

        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="admin_user_show")
     */
    public function userShow($id, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);

        $repo = $this->getDoctrine()->getRepository(Note::class);
        $notes = $repo->findByUser($id);



        return $this->render('admin/commercial.html.twig', [
            'user' => $user,
            'notes' => $notes
        ]);
    }

    /**
     * @Route("/admin/note/{id}", name="admin_note_show")
     */
    public function noteShow($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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

