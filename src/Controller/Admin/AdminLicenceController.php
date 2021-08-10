<?php


namespace App\Controller\Admin;


use App\Entity\Licence;
use App\Entity\Media;
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
    public function listCategory(LicenceRepository $licenceRepository, PaginatorInterface $paginator, Request $request)
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
                                   LicenceRepository $licenceRepository
    )
    {
        $licence = $licenceRepository->find($id);

        return $this->render('admin/licenceupdate.html.twig', [
            'licence' => $licence]);
    }

    /**
     * @Route("/admin/licence/save/{id}", name="admin_licence_save")
     */
    public function saveLicence($id,
                                 Request $request,
                                 LicenceRepository $licenceRepository,
                                 EntityManagerInterface $entityManager
    )
    {
        //dump($request);
        //die;
        $licence = $licenceRepository->find($id);

        $name = $request->request->get('name');
        $description = $request->request->get('description');

        $dossier = 'img/media/';
        $nom_fichier = $_FILES['image']['name'];
        $fichier = $_FILES['image']['tmp_name'];
        $type = $_FILES['image']['type'];
        $dossier_image = $dossier . $nom_fichier;
        move_uploaded_file($fichier, $dossier . $nom_fichier );
        $src = $nom_fichier;

        $licence->setName($name);
        $licence->setDescription($description);
        $licence->setMedia($src);

        $entityManager->persist($licence);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre licence est modifiée');

        return $this->redirectToRoute('admin_list_licences');
    }

    /**
     * @Route("/admin/licence/add/", name="admin_licence_add")
     */
    public function licenceAdd()
    {
        return $this->render('admin/adminlicenceadd.html.twig');
    }

    /**
     * @Route("/admin/licence/add/save/", name="admin_licence_add_save")
     */
    public function saveAddLicence(
        Request $request,
        EntityManagerInterface $entityManager
    )
    {
        //dump($request);
        //die;
        $licence= new Licence();

        $name = $request->request->get('name');
        $description = $request->request->get('description');

        $dossier = 'img/media/';
        $nom_fichier = $_FILES['image']['name'];
        $fichier = $_FILES['image']['tmp_name'];
        $type = $_FILES['image']['type'];
        $dossier_image = $dossier . $nom_fichier;
        move_uploaded_file($fichier, $dossier . $nom_fichier );
        $src = $nom_fichier;

        $licence->setName($name);
        $licence->setDescription($description);
        $licence->setMedia($src);

        $entityManager->persist($licence);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre licence est ajoutée');

        return $this->redirectToRoute('admin_list_licences');
    }

}