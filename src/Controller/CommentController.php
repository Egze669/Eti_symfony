<?php

namespace App\Controller;

use App\Entity\PostComment;
use App\Entity\User;
use App\Form\PostCommentFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class CommentController extends AbstractController
{

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param $idcomment integer
     * @param $idpost integer
     * @return Response
     */
    public function deleteComment(Request $request, ManagerRegistry $doctrine, $idcomment, $idpost): Response
    {
        $entityManager = $doctrine->getManager();
        $comment = $doctrine->getRepository(PostComment::class)->find($idcomment);
        $entityManager->remove($comment);
        $entityManager->flush();
        return $this->redirectToRoute('showpost', ['id' => $idpost]);
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param $idcomment
     * @param $idpost
     * @return Response
     */
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
                return $this->redirectToRoute('showpost', ['id' => $idpost]);
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

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param $author
     * @param $idpost
     * @return Response
     */
    public function revokeCommentPrivilege(Request $request, ManagerRegistry $doctrine, $author, $idpost): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->findOneBy(array('username' => $author));
        $user->setRoles([]);

        $entityManager->flush();
        return $this->redirectToRoute('showpost', ['id' => $idpost]);
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param $author
     * @param $idpost
     * @return Response
     */
    public function addCommentPrivilege(Request $request, ManagerRegistry $doctrine, $author, $idpost): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->findOneBy(array('username' => $author));
        $user->setRoles(["ROLE_COMMENTER"]);

        $entityManager->flush();
        return $this->redirectToRoute('showpost', ['id' => $idpost]);
    }


}