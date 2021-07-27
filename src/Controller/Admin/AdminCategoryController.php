<?php


namespace App\Controller\Admin;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategoryController extends AbstractController
{
    /**
     * @Route("/admin/categories/", name="admin_list_categories")
     */
    public function listCategory(CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request)
    {
        $categories = $categoryRepository->findAll();
        $categories6 = $paginator->paginate($categories, $request->query->getInt('page', 1), 6);
        return $this->render('admin/categories.html.twig', ['categories' => $categories6]);
    }

    /**
     * @Route("/admin/category/delete/{id}", name="admin_delete_category")
     */
    public function deleteProduct($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {
        $category = $categoryRepository->find($id);
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre catégorie est supprimé');

        return $this->redirectToRoute('admin_list_categories');
    }

    /**
     * @Route("/admin/category/update/{id}", name="admin_category_update")
     */
    public function categoryUpdate($id,
                                  CategoryRepository $categoryRepository
                                  )
    {
        $categorie = $categoryRepository->find($id);

        return $this->render('admin/categoryupdate.html.twig', [
            'categorie' => $categorie]);
    }

    /**
     * @Route("/admin/categoty/save/{id}", name="admin_category_save")
     */
    public function saveCategory($id,
                                Request $request,
                                CategoryRepository $categoryRepository,
                                EntityManagerInterface $entityManager
                                )
    {
        //dump($request);
        //die;
        $category = $categoryRepository->find($id);

        $name = $request->request->get('name');
        $description = $request->request->get('description');

        $category->setName($name);
        $category->setDescription($description);

        $entityManager->persist($category);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre catégorie est modifiée');

        return $this->redirectToRoute('admin_list_categories');
    }

    /**
     * @Route("/admin/categorie/add/", name="admin_categorie_add")
     */
    public function categorieAdd()
    {
        return $this->render('admin/categoryadd.html.twig');
    }

    /**
     * @Route("/admin/categoty/add/save/", name="admin_category_add_save")
     */
    public function saveAddCategory(
                                 Request $request,
                                 EntityManagerInterface $entityManager
    )
    {
        //dump($request);
        //die;
        $category = new Category();

        $name = $request->request->get('name');
        $description = $request->request->get('description');

        $category->setName($name);
        $category->setDescription($description);

        $entityManager->persist($category);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre catégorie est ajoutée');

        return $this->redirectToRoute('admin_list_categories');
    }

}