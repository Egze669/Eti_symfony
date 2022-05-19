<?php

namespace App\Controller;

use App\Entity\PostComment;
use App\Entity\User;
use App\Form\BlogPostFormType;
use App\Form\PostCommentFormType;
use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryformType;

class BlogController extends AbstractController
{

    /**
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return Response
     */
    public function view(ManagerRegistry $doctrine, int $id): Response
    {
        if ($id != 0) {
            $category = $doctrine->getRepository(BlogCategory::class)->find($id);
            $posts = $category->getPost();
        } else {
            $posts = $doctrine->getRepository(BlogPost::class)->findAll();
        }
        return $this->render('blog/view.html.twig', [
            "posts" => $posts,
        ]);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return Response
     */
    public function showpost(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $post = $doctrine->getRepository(BlogPost::class)->find($id);
        $postcomments = $doctrine->getRepository(PostComment::class)->findby(array('comment' => $id));
        $users = $doctrine->getRepository(User::class)->findAll();

        $comment = new PostComment();
        $form = $this->createForm(PostCommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $comment->setAuthor($this->getUser()->getUsername());
                $comment->setComment($post);
                $comment->setCreatedAt(date_create('now', null));
                $em = $doctrine->getManager();
                $em->persist($comment);
                $em->flush();
                return $this->redirectToRoute('showpost', ['id' => $id]);
            } else {
                $this->addFlash(
                    'error',
                    'Something went wrong sorry!'
                );
            }
        }


        return $this->render('blog/showpost.html.twig', [
            "comments" => $postcomments,
            "post" => $post,
            "users" => $users,
            "form" => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $name = "Homepage";
        return $this->render('blog/index.html.twig', [
            "name" => $name,
        ]);
    }


    /**
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function list(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(BlogCategory::class);
        $blogCategories = $repository->findAll();

        return $this->render('blog/list.html.twig', [
            "blogCategories" => $blogCategories,
        ]);
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newcat(Request $request, ManagerRegistry $doctrine)
    {
        $this->denyAccessUnlessGranted('ROLE_REDACTOR');
        $category = new BlogCategory();

        $form = $this->createForm(CategoryformType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->addFlash(
                    'notice',
                    'You created your category congratz!'
                );
                $category->setCreatedAt(date_create('now', null));
                $em = $doctrine->getManager();
                $em->persist($category);
                $em->flush();
                return $this->redirectToRoute('list');
            } else {
                $this->addFlash(
                    'error',
                    'Something went wrong sorry!'
                );
            }
        }
        return $this->render('blog/newcat.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function newpost(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new BlogPost();
        $form = $this->createForm(BlogPostFormType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->addFlash(
                    'notice',
                    'You created your post congratz!'
                );
                $post->setCreatedAt(date_create('now', null));
                $em = $doctrine->getManager();
                $em->persist($post);
                $em->flush();
                return $this->redirectToRoute('list');
            } else {
                $this->addFlash(
                    'error',
                    'Something went wrong sorry!'
                );
            }
        }
        return $this->render('blog/newpost.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @return Response
     */
    public function contact(): Response
    {
        $name = "Contact";
        return $this->render('blog/contact.html.twig', [
            "name" => $name,
        ]);
    }


}