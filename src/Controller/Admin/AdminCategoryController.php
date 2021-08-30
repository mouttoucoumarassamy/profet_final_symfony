<?php


namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function deletecategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
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
                                  CategoryRepository $categoryRepository,
                                    Request $request,
                                    EntityManagerInterface $entityManager
                                  )
    {
        $category = $categoryRepository->find($id);

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $imageFile = $categoryForm->get('media')->getData();
            if ($imageFile) {
                $dossier = 'img/media/';
                $nom_fichier = $_FILES['category']['name']['media'];
                $fichier = $_FILES['category']['tmp_name']['media'];
                $type = $_FILES['category']['type']['media'];
                $dossier_image = $dossier . $nom_fichier;
                move_uploaded_file($fichier, $dossier . $nom_fichier);
                $category->setMedia($nom_fichier);

                // Enregistrement des données via $entityManager dans la BDD
                $entityManager->persist($category);
                $entityManager->flush();
                // Message qui confirme l'action à l'utlisateur
                $this->addFlash(
                    'notice',
                    'Votre catégorie est modifiée');

                // Redirection vers la page qui liste tous les produits
                return $this->redirectToRoute('admin_list_categories');
            }

        }
        return $this->render('admin/categoryadd.html.twig', [
            'categoryForm' => $categoryForm->createView()]);
    }


    /**
     * @Route("/admin/categorie/add/", name="admin_categorie_add")
     */
    public function categorieAdd(EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger)
    {
        $category = new Category();
        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $imageFile = $categoryForm->get('media')->getData();
            if ($imageFile) {
                $dossier = 'img/media/';
                $nom_fichier = $_FILES['category']['name']['media'];
                $fichier = $_FILES['category']['tmp_name']['media'];
                $type = $_FILES['category']['type']['media'];
                $dossier_image = $dossier . $nom_fichier;
                move_uploaded_file($fichier, $dossier . $nom_fichier );
                $category->setMedia($nom_fichier);

                // Enregistrement des données via $entityManager dans la BDD
                $entityManager->persist($category);
                $entityManager->flush();
                // Message qui confirme l'action à l'utlisateur
                $this->addFlash(
                    'notice',
                    'Votre catégorie est ajoutée');

                // Redirection vers la page qui liste tous les produits
                return $this->redirectToRoute('admin_list_categories');
            }

        }
        return $this->render('admin/categoryadd.html.twig', [
            'categoryForm' => $categoryForm->createView()]);
    }


}