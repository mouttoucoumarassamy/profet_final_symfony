<?php


namespace App\Controller\Admin;


use App\Entity\Media;
use App\Form\MediaType;
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
    public function updateMedia($id, MediaRepository $mediaRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $media = $mediaRepository->find($id);
        $mediaForm = $this->createForm(MediaType::class, $media);
        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            $imageFile = $mediaForm->get('src')->getData();
            if ($imageFile) {
                $dossier = 'img/media/';
                $nom_fichier = $_FILES['media']['name']['src'];
                $fichier = $_FILES['media']['tmp_name']['src'];
                $type = $_FILES['media']['type']['src'];
                $dossier_image = $dossier . $nom_fichier;
                move_uploaded_file($fichier, $dossier . $nom_fichier );
                $media->setSRC($nom_fichier);
                $title = $media->getTitle();
                $media->setAlt($title);
                // Enregistrement des données via $entityManager dans la BDD
                $entityManager->persist($media);
                $entityManager->flush();
                // Message qui confirme l'action à l'utlisateur
                $this->addFlash(
                    'notice',
                    'Votre image est modifiée');

                // Redirection vers la page qui liste tous les produits
                return $this->redirectToRoute('admin_list_medias');
            }

        }
        return $this->render('admin/addmedia.html.twig', [
            'mediaForm' => $mediaForm->createView()]);
    }

    /**
     * @Route("/admin/media/add/", name="admin_media_add")
     */
    public function addSaveMedia(EntityManagerInterface $entityManager, Request $request)
    {

        $media = new Media();
        $mediaForm = $this->createForm(MediaType::class, $media);
        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            $imageFile = $mediaForm->get('src')->getData();
            if ($imageFile) {
                $dossier = 'img/media/';
                $nom_fichier = $_FILES['media']['name']['src'];
                $fichier = $_FILES['media']['tmp_name']['src'];
                $type = $_FILES['media']['type']['src'];
                $dossier_image = $dossier . $nom_fichier;
                move_uploaded_file($fichier, $dossier . $nom_fichier );
                $media->setSRC($nom_fichier);
                $title = $media->getTitle();
                $media->setAlt($title);
                // Enregistrement des données via $entityManager dans la BDD
                $entityManager->persist($media);
                $entityManager->flush();
                // Message qui confirme l'action à l'utlisateur
                $this->addFlash(
                    'notice',
                    'Votre image est ajoutée');

                // Redirection vers la page qui liste tous les produits
                return $this->redirectToRoute('admin_list_medias');
            }

        }
        return $this->render('admin/addmedia.html.twig', [
            'mediaForm' => $mediaForm->createView()]);
    }
}