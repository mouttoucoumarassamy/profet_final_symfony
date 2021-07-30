<?php


namespace App\Controller\Admin;


use App\Entity\Media;
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
    public function updateMedia($id, MediaRepository $mediaRepository)
    {
        $media = $mediaRepository->find($id);
        return $this->render('admin/updatemedia.html.twig', ['media' => $media]);
    }

    /**
     * @Route("/admin/media/add/", name="admin_media_add")
     */
    public function addMedia()
    {
        return $this->render('admin/addmedia.html.twig');
    }

    /**
     * @Route("/admin/media/add/save/", name="admin_media_add_save")
     */
    public function addSaveMedia(EntityManagerInterface $entityManager, Request $request)
    {
        $dossier = 'img/media/';
        $nom_fichier = $_FILES['image']['name'];
        $fichier = $_FILES['image']['tmp_name'];
        $type = $_FILES['image']['type'];
        $dossier_image = $dossier . $nom_fichier;
        move_uploaded_file($fichier, $dossier . $nom_fichier );
        $title = $request->request->get('title');
        $src = $nom_fichier;
        $media = new Media();
        $media->setTitle($title);
        $media->setSrc($src);
        $media->setAlt($title);

        $entityManager->persist($media);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre image est modifiée');

        return $this->redirectToRoute('admin_list_medias');
    }

    /**
     * @Route("/admin/media/update/save/{id}", name="admin_media_update_save")
     */
    public function addUpdateSaveMedia($id,EntityManagerInterface $entityManager, Request $request, MediaRepository $mediaRepository)
    {
        $dossier = 'img/media/';
        $nom_fichier = $_FILES['image']['name'];
        $fichier = $_FILES['image']['tmp_name'];
        $type = $_FILES['image']['type'];
        $dossier_image = $dossier . $nom_fichier;
        move_uploaded_file($fichier, $dossier . $nom_fichier );
        $title = $request->request->get('title');
        $src = $nom_fichier;
        $media = $mediaRepository->find($id);
        $media->setTitle($title);
        $media->setSrc($src);
        $media->setAlt($title);

        $entityManager->persist($media);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre image est modifiée');

        return $this->redirectToRoute('admin_list_medias');
    }


}