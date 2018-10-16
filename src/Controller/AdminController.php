<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Form\ScanType;
use App\Entity\Article;
use App\Entity\Message;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends Controller
{
    /** 
     * @Route("/admin/home", name="admin_home")
     */
    public function home()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Message::class);
        $messagesNonLus = $repo->findBy([
            "destinataire" => $user,
            "statut" => 'Non lu'
        ]);

        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();

        return $this->render('admin/home.html.twig', [
            'user' => $user,
            'messagesNonLus' => $messagesNonLus,
            'articles' => $articles
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
            5
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
            5
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
     *  @Route("/admin/note/{id}", name="admin_add_scan")
     */
    public function addScan(Note $note, Request $request, ObjectManager $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $form = $this->createForm(ScanType::class, $note);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('scan')->getData();
            if ($file != null) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('scans_directory'),
                        $fileName
                    );
                } catch (FileException $e) {

                }

                $note->setScan($fileName);
            }

            $manager->persist($note);
            $manager->flush();

            return $this->redirectToRoute('admin_note_show', ['id' => $note->getId()]);

        }

        return $this->render('admin/note.html.twig', [
            'form' => $form->createView(),
            'note' => $note
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

    /**
     * @Route("/admin/note/{id}/delete", name="admin_note_delete")
     */
    public function noteDelete($id, ObjectManager $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $entityManager = $this->getDoctrine()->getManager();
        $note = $entityManager->getRepository(Note::class)->find($id);

        $entityManager->remove($note);
        $entityManager->flush();

        return $this->redirectToRoute('admin_waiting_notes');

        return $this->render('admin/note.html.twig', [
            'note' => $note,
        ]);
    }
}

