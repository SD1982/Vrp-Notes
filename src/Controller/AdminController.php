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
     * @Route("/admin", name="admin_dashboard")
     */
    public function adminDashboard(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
      
        /* @var $paginator \Knp\Component\Pager\Paginator */
        $paginator = $this->get('knp_paginator');
    
        // Paginate the results of the query
        $users = $paginator->paginate(
            // Doctrine Query, not results
            $users,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            15
        );

        $repo = $this->getDoctrine()->getRepository(Note::class);
        $notes = $repo->findAll();
        
            /* @var $paginator \Knp\Component\Pager\Paginator */
        $paginator = $this->get('knp_paginator');
        
             // Paginate the results of the query
        $notes = $paginator->paginate(
            // Doctrine Query, not results
            $notes,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            6
        );

        return $this->render('admin/dashboard.html.twig', [
            'notes' => $notes,
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="admin_user_show")
     */
    public function userShow($id, Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);

        $repo = $this->getDoctrine()->getRepository(Note::class);
        $notes = $repo->findByUser($id);

                    /* @var $paginator \Knp\Component\Pager\Paginator */
        $paginator = $this->get('knp_paginator');
        
                    // Paginate the results of the query
        $notes = $paginator->paginate(
                   // Doctrine Query, not results
            $notes,
                   // Define the page parameter
            $request->query->getInt('page', 1),
                   // Items per page
            6
        );

        return $this->render('admin/user.html.twig', [
            'user' => $user,
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

