<?php


namespace App\Controller\Front;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class FrontCategoryController extends AbstractController
{
    /**
     * @Route("/categories" , name="front_categories_list")
     */
    public function listCategorie(CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request)
    {
        $categories = $categoryRepository->findAll();
        $categories6 = $paginator->paginate($categories, $request->query->getInt('page', 1), 6);
        return $this->render('Front/categories.html.twig', ['categories' => $categories6]);
    }

    /**
     * @Route ("/category/{id}", name="front_categorie_show")
     */
    public function showCategory($id, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);
        return $this->render('Front/category.html.twig', ['category' => $category]);
    }


}