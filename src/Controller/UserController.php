<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class UserController extends Controller
{
    /** 
     * @Route("/user", name="user_dashboard")
     */
    public function userDashboard(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Note::class);

        $notes = $repo->findByUser($user);
      
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
                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file = $form->get('scan')->getData();
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('scans_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                }
        
                    // updates the 'brochure' property to store the PDF file name
                    // instead of its contents
                $note->setScan($fileName);
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
            'editMode' => $note->getId() !== null,
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