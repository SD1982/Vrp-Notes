<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Form\UserRegistrationType;
use App\Form\AdminRegistrationType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/admin/user/new", name="security_user_new")
     * @Route("/admin/user/{id}/edit", name="security_user_edit")
     */
    public function userGestion(User $user = null, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {

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

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('security/user_registration_form.html.twig', [
            'form' => $form->createView(),
            'editMode' => $user->getId() !== null
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login_form.html.twig');
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
    }
}
