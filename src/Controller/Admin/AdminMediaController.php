<?php


namespace App\Controller\Admin;


use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @Route("/admin/media/delete/{id}", name="admin_delete_media")
     */
    public function deleteMedia($id, MediaRepository $mediaRepository, EntityManagerInterface $entityManager)
    {
        $media = $mediaRepository->find($id);
        $entityManager->remove($media);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre image est supprimé');

        return $this->redirectToRoute('admin_list_medias');
    }

    /**
     * @Route("/admin/media/update/{id}", name="admin_update_media")
     */
    public function updateMedia(){
        return $this->render('admin/upadtemedia.html.twig');
    }
}