<?php


namespace App\Controller\Admin;


use App\Repository\MediaRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminMediaController extends AbstractController
{
    /**
     * @Route("/admin/medias/", name="admin_list_medias")
     */
    public function listMedias(MediaRepository $mediaRepository, PaginatorInterface $paginator, Request $request){
        $medias = $mediaRepository->findAll();
        $medias6 = $paginator->paginate($medias, $request->query->getInt('page', 1), 6);
        return $this->render('admin/medias.html.twig',['medias' => $medias6]);
    }

}