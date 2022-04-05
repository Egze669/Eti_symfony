<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\BlogPostFormType;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\FormTypeInterface;
use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use App\Repository\BlogCategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryformType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class BlogController extends AbstractController
{
    /*
     * Show full list of posts based on category or all of them
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

    /*
     * Show blog post in detail
     */
    public function showpost(ManagerRegistry $doctrine, int $id): Response
    {
        $post = $doctrine->getRepository(BlogPost::class)->find($id);
        return $this->render('blog/showpost.html.twig', [
            "post" => $post,
        ]);
    }

    /*
     * Base homepage
     */
    public function index(): Response
    {
        $name = "Homepage";
        return $this->render('blog/index.html.twig', [
            "name" => $name,
        ]);
    }

    /*
     * Show list of categories as well as buttons to add category and blog post
     */
    public function list(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(BlogCategory::class);
        $blogCategories = $repository->findAll();

        return $this->render('blog/list.html.twig', [
            "blogCategories" => $blogCategories,
        ]);
    }

    /*
     * Add new category.
     * Redirect to list and show notice if valid
     * Show error message if not valid
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

    /*
        * Add new Post.
        * Redirect to list and show notice if valid
        * Show error message if not valid
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


    /*
     * Show contact page
     */
    public function contact(): Response
    {
        $name = "Contact";
        return $this->render('blog/contact.html.twig', [
            "name" => $name,
        ]);
    }

    /*
     * Registration
     */
    public function register(UserPasswordHasherInterface $passwordHasher, Request $request, ManagerRegistry $doctrine)
    {
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
                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('list');
            } else {
                $this->addFlash(
                    'error',
                    'Something went wrong sorry!'
                );
            }
        }
        return $this->render('blog/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /*
     * login
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('blog/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }
}