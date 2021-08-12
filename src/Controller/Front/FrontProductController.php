<?php


namespace App\Controller\Front;


use App\Entity\Comment;
use App\Entity\Note;
use App\Form\CommentType;
use App\Form\NoteType;
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
    public function showProduct(ProductRepository $productRepository, $id, Request $request, EntityManagerInterface  $entityManager, UserRepository $userRepository)
    {
        $product = $productRepository->find($id);
        $comment = new Comment;
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        $user = $this->getUser();
        if($user){
            $user_mail = $user->getUserIdentifier();
            $user_true = $userRepository->findBy(['email' => $user_mail]);
        }

        $note = new Note();
        $noteForm = $this->createForm(NoteType::class, $note);
        $noteForm->handleRequest($request);

        if($noteForm->isSubmitted() && $noteForm->isValid()){
            $note->setDate(new \DateTime("NOW"));
            $note->setProduct($productRepository->find($id));
            $note->setUser($user_true[0]);
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('front_show_product', ['id' => $id]);
        }

        if($commentForm->isSubmitted() && $commentForm->isValid()){
            $comment->setDate(new \DateTime("NOW"));
            $comment->setProduct($productRepository->find($id));
            $comment->setUser($user_true[0]);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('front_show_product', ['id' => $id]);
        }

        return $this->render("Front/product.html.twig", ['product' => $product,
            'commentForm' => $commentForm->createView(),
            'noteForm' =>$noteForm->createView()]);
    }


    public function productsAll(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render('front/_products_all.html.twig', [
            'products' => $products
        ]);
    }

}