<?php
// src/Controller/LuckyController.php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends  AbstractController
{

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if($this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('list');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('blog/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }
}