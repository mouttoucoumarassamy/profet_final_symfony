<?php


namespace App\Controller\Admin;


use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController
{
    /**
     * @Route("/admin/products" , name="admin_list_product")
     */
    public function listProduct(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request)
    {
        $products = $productRepository->findAll();
        $products6 = $paginator->paginate($products, $request->query->getInt('page', 1), 6);
        return $this->render("Admin/products.html.twig", ['products' => $products6]);
    }

    /**
     * @Route("/admin/product/delete/{id}", name="admin_delete_product")
     */
    public function deleteProduct($id, ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $product = $productRepository->find($id);
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('admin_list_product');
    }

    /**
     * @Route("/admin/product/update/{id}", name="admin_product_update")
     */
    public function productUpdate($id,
                                  Request $request,
                                  
                                  ProductRepository $productRepository,
                                  EntityManagerInterface $entityManager): Response
    {
        //$product = $productRepository->find($id);

        //$title = $request->request->get('title');
        //$content = $request->request->get('content');
        //$is_published = $request->request->get('is_published');
        //$id_category = $categoryRepository->find($request->request->get('id_category')) ;
        //$id_tag = $tagRepository->find($request->request->get('id_tag'));

        //$product->setTitle($title);
        //$product->setContent($content);
        //$product->setIsPublished($is_published);
        //$product->setCategory($id_category);
        //$product->setTag($id_tag);

        //$entityManager->persist($product);
        //$entityManager->flush();

        //return $this->redirectToRoute('admin_product_list');

        $product = $productRepository->find($id);

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if($productForm->isSubmitted() && $productForm->isValid()){
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Votre produit est modifié');
            return $this->redirectToRoute('admin_list_product');
        }

        return $this->render('admin/productadd.html.twig', ['productForm' => $productForm->createView()]);
    }

}