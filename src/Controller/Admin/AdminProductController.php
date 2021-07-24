<?php


namespace App\Controller\Admin;


use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\MediaRepository;
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
        $this->addFlash(
            'notice',
            'Votre produit est supprimé');

        return $this->redirectToRoute('admin_list_product');
    }

    /**
     * @Route("/admin/product/update/{id}", name="admin_product_update")
     */
    public function productUpdate($id,
                                  CategoryRepository $categoryRepository,
                                  LicenceRepository $licenceRepository,
                                  ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);
        $categories = $categoryRepository->findAll();
        $licences = $licenceRepository->findAll();
        return $this->render('admin/productupdate.html.twig', ['product' => $product,
            'categories' => $categories,
            'licences' => $licences]);
    }

    /**
     * @Route("/admin/product/save/{id}", name="admin_product_save")
     */
    public function saveProduct($id,
                                Request $request,
                                EntityManagerInterface $entityManager,
                                ProductRepository $productRepository,
                                CategoryRepository $categoryRepository,
                                LicenceRepository $licenceRepository)
    {
        //dump($request);
        //die;
        $product = $productRepository->find($id);

        $name = $request->request->get('name');
        $price = $request->request->get('price');
        $stock = $request->request->get('stock');
        $id_category = $categoryRepository->find($request->request->get('id_category')) ;
        $id_licence = $licenceRepository->find($request->request->get('id_licence'));

        $product->setName($name);
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setCategory($id_category);
        $product->setLicence($id_licence);

        $entityManager->persist($product);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre produit est modifié');

        return $this->redirectToRoute('admin_list_product');

    }

    /**
     * @Route("/admin/product/add/", name="admin_product_add")
     */
    public function productAdd(
                                  CategoryRepository $categoryRepository,
                                  LicenceRepository $licenceRepository,
                                    MediaRepository $mediaRepository)
    {
        $medias = $mediaRepository->findAll();
        $categories = $categoryRepository->findAll();
        $licences = $licenceRepository->findAll();
        return $this->render('admin/productadd.html.twig', [
            'categories' => $categories,
            'licences' => $licences,
            'medias' => $medias]);
    }

    /**
     * @Route("/admin/product/add/save/", name="admin_product_save_add")
     */
    public function saveAddProduct(
                                Request $request,
                                EntityManagerInterface $entityManager,
                                ProductRepository $productRepository,
                                CategoryRepository $categoryRepository,
                                LicenceRepository $licenceRepository,
                                MediaRepository $mediaRepository)
    {
        //dump($request);
        //die;
        $product = new Product();

        $name = $request->request->get('name');
        $price = $request->request->get('price');
        $stock = $request->request->get('stock');
        //$media = $request->request->get('media');
        $medias = [];
        $mediaFounded =[];

        $mediasAll = $mediaRepository->findAll();
        $l = count($mediasAll);

        for($i = 1 ; $i <= $l; $i++){
            if(!empty($request->request->get('img'.$i))){
                $medias[] = $request->request->get('img'.$i);
            }
        };
        foreach ($medias as $media){
            $mediaUp[] = $mediaRepository->findBy(['src' => $media]);
            }

        if(!empty($mediaUp)){
            foreach ($mediaUp as $images => $img){
            dump($img[0])->setProduct($product);
            }
        }

        $id_category = $categoryRepository->find($request->request->get('id_category')) ;
        $id_licence = $licenceRepository->find($request->request->get('id_licence'));

        $product->setName($name);
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setCategory($id_category);
        $product->setLicence($id_licence);

        $entityManager->persist($product);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre produit est ajouté');

        return $this->redirectToRoute('admin_list_product');

    }
}