<?php


namespace App\Controller\Admin;

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

}