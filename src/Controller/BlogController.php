<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends  AbstractController
{

    /**
     * @throws \Exception
     */
    public function index(): Response
    {
        $name = "Homepage";
        return $this->render('blog/index.html.twig',[
            "name"=>$name,
        ]);
    }
    public function list(): Response
    {
        $name = "List";
        return $this->render('blog/list.html.twig',[
            "name"=>$name,
        ]);
    }
    public function login(): Response
    {
        $name = "Login";
        return $this->render('blog/login.html.twig',[
            "name"=>$name,
        ]);
    }
    public function contact(): Response
    {
        $name = "Contact";
        return $this->render('contact/contact.html.twig',[
            "name"=>$name,
        ]);
    }
}