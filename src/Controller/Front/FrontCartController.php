<?php


namespace App\Controller\Front;


use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class FrontCartController extends AbstractController
{

    /**
     * @Route("/cart/", name="cart")
     */
    public function indexCart(SessionInterface $session, ProductRepository $productRepository)
    {
        $panier = $session->get('panier', []);

        $panierWithData = [];
        foreach ($panier as $id => $quantity){
            $panierWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        return $this->render('front/panier.html.twig', ['items' => $panierWithData]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_cart")
     */
    public function addCart($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }


        $session->set('panier', $panier);

        return $this->redirectToRoute('front_show_product', ['id' => $id]);
    }

    /**
     * @Route("/cart/delete/", name="delete_cart")
     */
    public function deleteCart(SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        dd($panier);
    }


}