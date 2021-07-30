<?php


namespace App\Controller\Admin;



use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{

    /**
     * @Route("/admin/comments/", name="admin_list_comments")
     */
    public function listComment(CommentRepository $commentRepository, PaginatorInterface $paginator, Request $request)
    {
        $comments = $commentRepository->findAll();
        $comments6 = $paginator->paginate($comments, $request->query->getInt('page', 1), 6);
        return $this->render('admin/comments.html.twig', ['comments' => $comments6]);
    }
}