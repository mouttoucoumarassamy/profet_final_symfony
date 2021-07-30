<?php


namespace App\Controller\Admin;



use App\Repository\NoteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminNoteController extends AbstractController
{

    /**
     * @Route("/admin/notes/", name="admin_list_notes")
     */
    public function listNote(NoteRepository $noteRepository, PaginatorInterface $paginator, Request $request)
    {
        $notes = $noteRepository->findAll();
        $notes6 = $paginator->paginate($notes, $request->query->getInt('page', 1), 6);
        return $this->render('admin/notes.html.twig', ['notes' => $notes6]);
    }
}