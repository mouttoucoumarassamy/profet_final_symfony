<?php


namespace App\Controller\Front;

use App\Repository\LicenceRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontLicenceController extends AbstractController
{
    /**
     * @Route("/licences/", name="front_list_licence")
     */
    public function listLicence(LicenceRepository $licenceRepository)
    {
        $licences = $licenceRepository->findAll();
        return $this->render("Front/licences.html.twig", ['licences' => $licences]);
    }

    /**
     * @Route("/licence/{id}", name="front_show_licence")
     */
    public function showLicence($id, LicenceRepository $licenceRepository)
    {
        $licence = $licenceRepository->find($id);
        return $this->render("Front/licence.html.twig", ['licence' => $licence, 'id' => $id]);
    }
}