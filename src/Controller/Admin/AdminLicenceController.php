<?php


namespace App\Controller\Admin;


use App\Entity\Licence;
use App\Entity\Media;
use App\Form\LicenceType;
use App\Repository\LicenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminLicenceController extends AbstractController
{
    /**
     * @Route("/admin/licences/", name="admin_list_licences")
     */
    public function listlicence(LicenceRepository $licenceRepository, PaginatorInterface $paginator, Request $request)
    {
        $licences = $licenceRepository->findAll();
        $licences6 = $paginator->paginate($licences, $request->query->getInt('page', 1), 6);
        return $this->render('admin/licences.html.twig', ['licences' => $licences6]);
    }

    /**
     * @Route("/admin/licence/delete/{id}", name="admin_delete_licence")
     */
    public function deleteLicence($id, LicenceRepository $licenceRepository, EntityManagerInterface $entityManager)
    {
        $licence = $licenceRepository->find($id);
        $entityManager->remove($licence);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre licence est supprimé');

        return $this->redirectToRoute('admin_list_licences');
    }

    /**
     * @Route("/admin/licence/update/{id}", name="admin_licence_update")
     */
    public function licenceUpdate($id,
                                   LicenceRepository $licenceRepository,
                                    Request $request,
                                    EntityManagerInterface $entityManager
    )
    {
        $licence = $licenceRepository->find($id);

        $licenceForm = $this->createForm(LicenceType::class, $licence);

        $licenceForm->handleRequest($request);

        if ($licenceForm->isSubmitted() && $licenceForm->isValid()) {
            $imageFile = $licenceForm->get('media')->getData();
            if ($imageFile) {
                $dossier = 'img/media/';
                $nom_fichier = $_FILES['licence']['name']['media'];
                $fichier = $_FILES['licence']['tmp_name']['media'];
                $type = $_FILES['licence']['type']['media'];
                $dossier_image = $dossier . $nom_fichier;
                move_uploaded_file($fichier, $dossier . $nom_fichier);
                $licence->setMedia($nom_fichier);

                // Enregistrement des données via $entityManager dans la BDD
                $entityManager->persist($licence);
                $entityManager->flush();
                // Message qui confirme l'action à l'utlisateur
                $this->addFlash(
                    'notice',
                    'Votre licence est modifiée');

                // Redirection vers la page qui liste tous les produits
                return $this->redirectToRoute('admin_list_licences');
            }

        }
        return $this->render('admin/adminlicenceadd.html.twig', [
            'licenceForm' => $licenceForm->createView()]);

       
    }

    
    /**
     * @Route("/admin/licence/add/", name="admin_licence_add")
     */
    public function licenceAdd(Request $request, EntityManagerInterface $entityManager)
    {
        $licence = new licence();
        $licenceForm = $this->createForm(LicenceType::class, $licence);

        $licenceForm->handleRequest($request);

        if ($licenceForm->isSubmitted() && $licenceForm->isValid()) {
            $imageFile = $licenceForm->get('media')->getData();
            if ($imageFile) {
                $dossier = 'img/media/';
                $nom_fichier = $_FILES['licence']['name']['media'];
                $fichier = $_FILES['licence']['tmp_name']['media'];
                $type = $_FILES['licence']['type']['media'];
                $dossier_image = $dossier . $nom_fichier;
                move_uploaded_file($fichier, $dossier . $nom_fichier );
                $licence->setMedia($nom_fichier);

                // Enregistrement des données via $entityManager dans la BDD
                $entityManager->persist($licence);
                $entityManager->flush();
                // Message qui confirme l'action à l'utlisateur
                $this->addFlash(
                    'notice',
                    'Votre licence est ajoutée');

                // Redirection vers la page qui liste tous les produits
                return $this->redirectToRoute('admin_list_licences');
            }

        }
        return $this->render('admin/adminlicenceadd.html.twig', [
            'licenceForm' => $licenceForm->createView()]);
    }
    

}