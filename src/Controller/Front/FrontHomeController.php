<?php


namespace App\Controller\Front;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class FrontHomeController extends AbstractController
{
    /**
     * @Route("/", name="front_home")
     */
    public function home(CategoryRepository $categoryRepository)
    {
        $categories =$categoryRepository->findAll();
        $id = rand(1, count($categories));
        $categorie = $categoryRepository->find($id);
        if($categorie){
            return $this->render('front/home.html.twig', ['categorie' => $categorie]);
        }else{
            return $this->redirectToRoute('front_home');
        }
    }

    /**
     * @Route("/search/", name="front_search")
     */
    public function search(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator)
    {
        $term = $request->query->get('q');
        $products = $productRepository->searchByTerm($term);
        $products6 = $paginator->paginate($products, $request->query->getInt('page', 1), 6);
        return $this->render('front/search.html.twig', ['products' => $products6, 'term' => $term]);
    }

}