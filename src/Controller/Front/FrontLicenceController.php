<?php


namespace App\Controller\Front;

use App\Repository\LicenceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\Pagination\PaginationInterface;
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
    public function showLicence($id, LicenceRepository $licenceRepository, PaginatorInterface $paginator, Request $request)
    {
        $licence = $licenceRepository->find($id);
        $products = $licence->getProducts();
        $products6 = $paginator->paginate($products, $request->query->getInt('page', 1), 6);
        return $this->render("Front/licence.html.twig", ['licence' => $licence, 'id' => $id,
             "products" => $products6]);
    }

    public function licencesAll(LicenceRepository $licenceRepository)
    {
        $licences = $licenceRepository->findAll();

        return $this->render('front/_licences_all.html.twig', [
            'licences' => $licences
        ]);
    }
}