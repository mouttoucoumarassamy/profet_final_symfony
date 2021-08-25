<?php


namespace App\Controller\Admin;


use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $this->addFlash(
            'notice',
            'Votre produit est supprimé');

        return $this->redirectToRoute('admin_list_product');
    }

    /**
     * @Route("/admin/product/update/{id}", name="admin_product_update")
     */
    public function productUpdate($id, ProductRepository $productRepository, Request $request,EntityManagerInterface $entityManager)
    {
        $product = $productRepository->find($id);
        $productForm = $this->createForm(ProductType::class, $product);
        $productForm->handleRequest($request);

        if($productForm->isSubmitted() && $productForm->isValid()){

            $media = $product->getMedia();
            foreach ($media as $image){
                $image->setProduct($product);
            }
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Votre produit est modifié');

            return $this->redirectToRoute('admin_list_product');
        }
        return $this->render('admin/productadd.html.twig', [
            'productForm' => $productForm->createView()]);
    }


    /**
     * @Route("/admin/product/add/", name="admin_product_add")
     */
    public function productAdd(Request $request,EntityManagerInterface $entityManager)
    {
        $product = new Product();
        $productForm = $this->createForm(ProductType::class, $product);
        $productForm->handleRequest($request);

        if($productForm->isSubmitted() && $productForm->isValid()){

                $media = $product->getMedia();
                foreach ($media as $image){
                    $image->setProduct($product);
                }
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Votre produit est ajouté');

            return $this->redirectToRoute('admin_list_product');
        }
        return $this->render('admin/productadd.html.twig', [
        'productForm' => $productForm->createView()]);
    }

}