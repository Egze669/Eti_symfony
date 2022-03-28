<?php
namespace App\Controller;
use App\Form\BlogPostFormType;
use Symfony\Component\Form\FormTypeInterface;
use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use App\Repository\BlogCategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryformType;

class BlogController extends  AbstractController
{

    public function view(ManagerRegistry $doctrine,int $id): Response
    {
        if($id != 0 ) {
            $category = $doctrine->getRepository(BlogCategory::class)->find($id);
            $posts = $category->getPost();
        }
        else{
            $posts = $doctrine->getRepository(BlogPost::class)->findAll();
        }
        return $this->render('blog/view.html.twig',[
            "posts"=>$posts,
        ]);
    }
    public function showpost(ManagerRegistry $doctrine,int $id): Response
    {
        $post= $doctrine->getRepository(BlogPost::class)->find($id);
        return $this->render('blog/showpost.html.twig',[
            "post"=>$post,
        ]);
    }
    public function index(): Response
    {
        $name = "Homepage";
        return $this->render('blog/index.html.twig',[
            "name"=>$name,
        ]);
    }

    public function list(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(BlogCategory::class);
        $blogCategories = $repository->findAll();

        return $this->render('blog/list.html.twig',[
            "blogCategories"=>$blogCategories,
        ]);
    }
    public function newcat(Request $request,ManagerRegistry $doctrine)
    {
        $category = new BlogCategory();

        $form = $this->createForm(CategoryformType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid())
        {
            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();
        }

        return $this->render('blog/newcat.html.twig',[
            'form'=>$form->createView(),
        ]);
    }
    public function newpost(Request $request,ManagerRegistry $doctrine): Response
    {
    $post = new BlogPost();

        $form = $this->createForm(BlogPostFormType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid())
        {
            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();
        }
    return $this->render('blog/newpost.html.twig',[
        'form'=>$form->createView(),
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