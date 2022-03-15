<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends  AbstractController
{

    /**
     * @throws \Exception
     */
    public function view(Request $req,string $urlkey): Response
    {
        $urkey = $req->query->get('urkey');
        return $this->render('blog/view.html.twig',[
            "urkey"=>$urkey,
        ]);
    }
    public function index(): Response
    {
        $name = "Homepage";
        return $this->render('blog/index.html.twig',[
            "name"=>$name,
        ]);
    }
    public function list(Request $req, int $page): Response
    {
        $page = $req->query->get('page');
        return $this->render('blog/list.html.twig',[
            "page"=>$page,
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
        return $this->render('blog/contact.html.twig',[
            "name"=>$name,
        ]);
    }
}