<?php


namespace App\Controller\Front;


use App\Entity\Comment;
use App\Entity\Note;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
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

    /**
     * @Route("/comment/{id}", name="front_add_comment")
     */
    public function addComment(ProductRepository $productRepository, $id, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $comment = new Comment();
        $user = $this->getUser();
        $user_mail = $user->getUserIdentifier();
        $user_true = $userRepository->findBy(['email' => $user_mail]);
        $comment->setContent($request->request->get('content'));
        $comment->setProduct($productRepository->find($id));
        $comment->setDate(new \DateTime("NOW"));
        $comment->setUser($user_true[0]);

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('front_show_product', ['id' => $id]);
    }

    /**
     * @Route("/note/{id}", name="front_add_note")
     */
    public function addNote(ProductRepository $productRepository, $id, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $note = new Note();
        $user = $this->getUser();
        $user_mail = $user->getUserIdentifier();
        $user_true = $userRepository->findBy(['email' => $user_mail]);
        $note->setNote($request->request->get('note'));
        $note->setProduct($productRepository->find($id));
        $note->setDate(new \DateTime("NOW"));
        $note->setUser($user_true[0]);

        $entityManager->persist($note);
        $entityManager->flush();

        return $this->redirectToRoute('front_show_product', ['id' => $id]);
    }

    public function productsAll(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render('front/_products_all.html.twig', [
            'products' => $products
        ]);
    }

}