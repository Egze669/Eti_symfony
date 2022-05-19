<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */


    public function register(UserPasswordHasherInterface $passwordHasher, Request $request, ManagerRegistry $doctrine)
    {
        if ($this->isGranted('ROLE_USER')) {
            ;
            return $this->redirectToRoute('list');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $plainPassword = $form->get('plainPassword')->getData();
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                );
                $user->setPassword($hashedPassword);
                $this->addFlash(
                    'notice',
                    'Welcome new user!'
                );
                $user->setRoles(['ROLE_COMMENTER']);
                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('list');
            }
        }
        return $this->render('blog/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
