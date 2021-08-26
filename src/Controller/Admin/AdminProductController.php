<?php


namespace App\Controller\Admin;


use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\MediaRepository;
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
    public function productUpdate($id,
                                  ProductRepository $productRepository,
                                  Request $request,
                                  EntityManagerInterface $entityManager,
                                  MediaRepository $mediaRepository)
    {
        // On récupèr ele produit via son id
        $product = $productRepository->find($id);

        // On crée le formulaire
        $productForm = $this->createForm(ProductType::class, $product);
        $productForm->handleRequest($request);

        if($productForm->isSubmitted() && $productForm->isValid()){
            //On récupère toutes les images qui ont le plus à modifier comme produit associé
           $medias =  $mediaRepository->findBy(['product' => $product]);
           // A chaque image on on enlève le produit associé
           foreach ($medias as $media){
               $media->setProduct(null);
           }
            // Récupération des images du produit
            $media = $product->getMedia();
            // A chaque image on lui attribue le produit
            foreach ($media as $image){
                $image->setProduct($product);
            }
            // Enregistrement des données via $entityManager dans la BDD
            $entityManager->persist($product);
            $entityManager->flush();
            // Message qui confirme l'action à l'utlisateur
            $this->addFlash(
                'notice',
                'Votre produit est modifié');

            // Redirection vers la page qui liste tous les produit
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
        // Instance d'un nouveau produit
        $product = new Product();
        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if($productForm->isSubmitted() && $productForm->isValid()){
                // Récupération des images du produit
                $media = $product->getMedia();
                // A chaque image on lui attribue le produit
                foreach ($media as $image){
                    $image->setProduct($product);
                }
                // Enregistrement des données via $entityManager dans la BDD
            $entityManager->persist($product);
            $entityManager->flush();
            // Message qui confirme l'action à l'utlisateur
            $this->addFlash(
                'notice',
                'Votre produit est ajouté');

            // Redirection vers la page qui liste tous les produits
            return $this->redirectToRoute('admin_list_product');
        }

        return $this->render('admin/productadd.html.twig', [
        'productForm' => $productForm->createView()]);
    }

}