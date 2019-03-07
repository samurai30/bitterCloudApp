<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="user_register")
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(UserPasswordEncoderInterface $encoder,Request $request)
    {
         $user = new User();
         $form = $this->createForm(UserType::class,$user);

         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){
            $password = $encoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($password);
            $user->setRoles([User::ROLE_USER]);
            $entityManget = $this->getDoctrine()->getManager();
            $entityManget->persist($user);
            $entityManget->flush();

            return $this->redirectToRoute('security_login');
         }

        return $this->render('register/index.html.twig',
            [
                'form' => $form->createView()
            ]);
    }
}
