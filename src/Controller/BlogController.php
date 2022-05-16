<?php

namespace App\Controller;

use App\Entity\PostComment;
use App\Entity\User;
use App\Form\BlogPostFormType;
use App\Form\PostCommentFormType;
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
        $postcomments = $doctrine->getRepository(PostComment::class)->findby(array('comment'=>$id));
        $users = $doctrine->getRepository(User::class)->findAll();

        $comment = new PostComment();
        $form = $this->createForm(PostCommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $comment->setAuthor($this->getUser()->getUsername());
                $comment->setComment($post);
                $comment->setCreatedAt(date_create('now',null));
                $em = $doctrine->getManager();
                $em->persist($comment);
                $em->flush();
                return $this->redirectToRoute('showpost',['id'=>$id]);
            } else {
                $this->addFlash(
                    'error',
                    'Something went wrong sorry!'
                );
            }
        }


        return $this->render('blog/showpost.html.twig', [
            "comments"=>$postcomments,
            "post" => $post,
            "users" => $users,
            "form" => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param $idcomment integer
     * @param $idpost intger
     * @return Response
     */
    public function deleteComment(Request $request, ManagerRegistry $doctrine, $idcomment, $idpost): Response
    {
        $entityManager = $doctrine->getManager();
        $comment = $doctrine->getRepository(PostComment::class)->find($idcomment);
        $entityManager->remove($comment);
        $entityManager->flush();
        return $this->redirectToRoute('showpost',['id'=>$idpost]);
    }
    public function editComment(Request $request, ManagerRegistry $doctrine, $idcomment, $idpost): Response
    {
        $postcomment = $doctrine->getRepository(PostComment::class)->find($idcomment);

        $form = $this->createForm(PostCommentFormType::class, $postcomment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $doctrine->getManager();
                $em->persist($postcomment);
                $em->flush();
                return $this->redirectToRoute('showpost',['id'=>$idpost]);
            } else {
                $this->addFlash(
                    'error',
                    'Something went wrong sorry!'
                );
            }
        }


        return $this->render('blog/editcomment.html.twig', [
            "form" => $form->createView(),
        ]);
    }
    public function revokeCommentPrivilege(Request $request, ManagerRegistry $doctrine, $author,$idpost): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->findOneBy(array('username'=>$author));
        $user->setRoles([]);

        $entityManager->flush();
        return $this->redirectToRoute('showpost',['id'=>$idpost]);
    }
    public function addCommentPrivilege(Request $request, ManagerRegistry $doctrine, $author,$idpost): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->findOneBy(array('username'=>$author));
        $user->setRoles(["ROLE_COMMENTER"]);

        $entityManager->flush();
        return $this->redirectToRoute('showpost',['id'=>$idpost]);
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
                $category->setCreatedAt(date_create('now',null));
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
                $post->setCreatedAt(date_create('now',null));
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

    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function register(UserPasswordHasherInterface $passwordHasher, Request $request, ManagerRegistry $doctrine)
    {
        if($this->isGranted('ROLE_USER')){;
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