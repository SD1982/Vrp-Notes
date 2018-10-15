<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Form\ScanType;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class UserController extends Controller
{
    /** 
     * @Route("/user/home", name="user_home")
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

        return $this->render('user/home.html.twig', [
            'user' => $user,
            'messagesNonLus' => $messagesNonLus
        ]);
    }

    /** 
     * @Route("/user/validated", name="user_validated_notes")
     */
    public function validatedNotes(Request $request)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Note::class);

        $validatedNotes = $repo->findBy([
            "user" => $user,
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

        return $this->render('user/validated_notes.html.twig', [
            'validated_notes' => $validatedNotes,
            'user' => $user
        ]);
    }

    /** 
     * @Route("/user/waiting", name="user_waiting_notes")
     */
    public function waitingNotes(Request $request)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Note::class);

        $notValidatedNotes = $repo->findBy([
            "user" => $user,
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

        return $this->render('user/waiting_notes.html.twig', [
            'not_validated_notes' => $notValidatedNotes,
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

            }

            $manager->persist($note);
            $manager->flush();

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $user = $this->getUser();
                // On récupère la liste des rôles d'un utilisateur
            $roles = $user->getRoles();
                // On transforme le tableau d'instance en tableau simple
            $rolesTab = array_map(function ($role) {
                return $role;
            }, $roles);
                // Si admin redirection vers le dashboard admin
            if (in_array('ROLE_ADMIN', $rolesTab, true)) {
                return $this->redirectToRoute('admin_note_show', ['id' => $note->getId()]);
                // Si user redirection vers le dashboard user
            } elseif (in_array('ROLE_USER', $rolesTab, true)) {
                return $this->redirectToRoute('user_note_show', ['id' => $note->getId()]);
            }
        }

        return $this->render('user/note_registration_form.html.twig', [
            'form' => $form->createView(),
            'adminEditMode' => $note->getId() !== null,
            'note' => $note
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }


    /**
     *  @Route("/user/note/{id}", name="user_add_scan")
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

            return $this->redirectToRoute('user_note_show', ['id' => $note->getId()]);

        }

        return $this->render('user/note.html.twig', [
            'form' => $form->createView(),
            'note' => $note
        ]);
    }

    /**
     * @Route("/user/note/{id}", name="user_note_show")
     */
    public function noteShow($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Note::class);

        $note = $repo->find($id);

        return $this->render('user/note.html.twig', [
            'note' => $note
        ]);
    }

    /**
     * @Route("/user/map", name="user_map")   
     */
    public function markersMap(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Note::class);

        $notes = $repo->findByUser($user);

        if ($request->isXmlHttpRequest()) {

            $jsonData = array();
            $idx = 0;
            foreach ($notes as $note) {
                $noteInfos = array(
                    'date' => $note->getDate(),
                    'montant' => $note->getMontant(),
                    'type' => $note->getType(),
                    'statut' => $note->getStatut(),
                    'address' => $note->getAdress(),
                    'postcode' => $note->getPostcode(),
                    'city' => $note->getCity(),
                    'country' => $note->getCountry(),
                    'lat' => $note->getLatitude(),
                    'lng' => $note->getLongitude(),
                    'description' => $note->getDescription(),
                );
                $jsonData[$idx++] = $noteInfos;
            }
            return new JsonResponse($jsonData);

        } else {

            return $this->render('user/map.html.twig', [
                'notes' => $notes
            ]);
        }
    }

}