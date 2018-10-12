<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Form\UserRegistrationType;
use App\Form\AdminRegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController
{
    /**
     * @Route("/admin/add/user", name="security_user_add")
     * @Route("/admin/user/{id}/edit", name="security_user_edit")
     */
    public function userGestion(User $user = null, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();

        if ($request->isXmlHttpRequest()) {

            $jsonData = array();
            $idx = 0;
            foreach ($users as $user) {
                $usersInfos = array(
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                );
                $jsonData[$idx++] = $usersInfos;
            }
            return new JsonResponse($jsonData);
        }

        if (!$user) {
            $user = new User();
        }

        $form = $this->createForm(UserRegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user->getId()) {
                $user->setCreatedAt(new \DateTime());
                $user->setRoles(['ROLE_USER']);
            }

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render('security/user_registration_form.html.twig', [
            'form' => $form->createView(),
            'editMode' => $user->getId() !== null,
            'user' => $user
        ]);
    }

    /**
     * @Route("/", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login_form.html.twig');
    }

    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
            // On récupère la liste des rôles d'un utilisateur
        $roles = $user->getRoles();
            // On transforme le tableau d'instance en tableau simple
        $rolesTab = array_map(function ($role) {
            return $role;
        }, $roles);
            // Si admin redirection vers le home admin
        if (in_array('ROLE_ADMIN', $rolesTab, true)) {
            return $this->redirectToRoute('admin_home');
            // Si user redirection vers le home user
        } elseif (in_array('ROLE_USER', $rolesTab, true)) {
            return $this->redirectToRoute('user_home');
        }
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
    }
}
