<?php


namespace App\Controller\Front;


use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class FrontProductController extends AbstractController
{

    /**
     * @Route("/products" , name="front_list_product")
     */
    public function listProduct(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request)
    {
        $products = $productRepository->findAll();
        $products6 = $paginator->paginate($products, $request->query->getInt('page', 1), 6);
        return $this->render("Front/products.html.twig", ['products' => $products6]);
    }

    /**
     * @Route("/product/{id}", name="front_show_product")
     */
    public function showProduct(ProductRepository $productRepository, $id)
    {
        $product = $productRepository->find($id);
        return $this->render("Front/product.html.twig", ['product' => $product]);
    }


}