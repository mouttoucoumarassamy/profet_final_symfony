<?php


namespace App\Controller\Front;


use App\Entity\Command;
use App\Form\UserType;
use App\Repository\CommandRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
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
     * @Route("/cart/delete/{id}", name="delete_cart")
     */
    public function deleteCart($id,SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if(!empty($panier[$id]) && $panier[$id] == 1 ){
            unset($panier[$id]);
        }else{
            $panier[$id]--;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/infos/", name="info_cart")
     */
    public function infosCart(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager)
    {

        $user = $this->getUser();

        if($user){
            $user_mail = $user->getUserIdentifier();
            $user_true = $userRepository->findBy(['email' => $user_mail]);
            return $this->render('front/infos-cart.html.twig', ['user' => $user_true]);
        }else{
            return $this->render('front/infos-cart.html.twig');
        }

    }

    /**
     * @Route("/command/info/", name="info_command")
     */
    public function infoCard(SessionInterface $session,
                             ProductRepository $productRepository,
                             UserRepository $userRepository,
                             Request $request,
                             EntityManagerInterface $entityManager,
                             CommandRepository $commandRepository
                             )
    {
        $panier = $session->get('panier', []);
        $p = 0;
        foreach ( $panier as $prod => $quantity){
            $product = $productRepository->find($prod);
            $price_product = $product->getPrice();
            $p = $p + $price_product*$quantity;
        }
        $user = $this->getUser();
        if ($user){
            $user_mail = $user->getUserIdentifier();
            $user_true = $userRepository->findBy(['email' => $user_mail]);

            $email = $request->request->get('email');
            $adress = $request->request->get('adress');
            $city = $request->request->get('city');
            $zipcode = $request->request->get('zipcode');

            $user_true[0]->setEmail($email);
            $user_true[0]->setAdress($adress);
            $user_true[0]->setCity($city);
            $user_true[0]->setZipcode($zipcode);

            $entityManager->persist($user_true[0]);
            $entityManager->flush();

            $commandall = $commandRepository->findAll();
            $commandlist = count($commandall);

            $command = new Command();

            $number = $commandlist + 1;
            $command->setNumberOrder('Commd -' . $number );
            $command->setDate(new \DateTime("NOW"));
            $command->setUser($user_true[0]);
            $command->setPrice($p);

            $entityManager->persist($command);
            $entityManager->flush();

            $id = $command->getId();

            foreach ( $panier as $prod => $quantity){
                $connexionBdd = mysqli_connect("localhost", "root", "root");
                $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
                $product = $productRepository->find($prod);
                $stock_begin = $product->getStock();
                $stock_end = $stock_begin - $quantity ;
                $product->setStock($stock_end);
                $requete = "INSERT INTO product_command (product_amount, product_id, command_id) VALUES (". $quantity .", ". $product->getId().", ". $command->getId() .")";
                $resultat = mysqli_query($connexionBdd, $requete);
                mysqli_close($connexionBdd);


                unset($panier[$prod]);
                $session->set('panier', $panier);

                $entityManager->persist($product);
                $entityManager->flush();

            }

            return $this->redirectToRoute('card_infos', ['id' => $id]);
        }else{

            $email = $request->request->get('email');
            $adress = $request->request->get('adress');
            $city = $request->request->get('city');
            $zipcode = $request->request->get('zipcode');

            $commandall = $commandRepository->findAll();
            $commandlist = count($commandall);

            $command = new Command();
            $number = $commandlist + 1;
            $command->setNumberOrder('Command -' . $number );
            $command->setDate(new \DateTime("NOW"));
            $command->setEmail($email);
            $command->setAdress($adress);
            $command->setCity($city);
            $command->setZipcode($zipcode);
            $command->setPrice($p);

            $entityManager->persist($command);
            $entityManager->flush();

            $id = $command->getId();

            foreach ( $panier as $prod => $quantity){
                $connexionBdd = mysqli_connect("localhost", "root", "root");
                $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
                $product = $productRepository->find($prod);
                $stock_begin = $product->getStock();
                $stock_end = $stock_begin - $quantity ;
                $product->setStock($stock_end);
                $requete = "INSERT INTO product_command (product_amount, product_id, command_id) VALUES (". $quantity .", ". $product->getId().", ". $command->getId() .")";
                $resultat = mysqli_query($connexionBdd, $requete);
                mysqli_close($connexionBdd);

                unset($panier[$prod]);
                $session->set('panier', $panier);

                $entityManager->persist($command);
                $entityManager->flush();


            }
            return $this->redirectToRoute('card_infos', ['id' => $id]);
        }

    }

    /**
     * @Route("/cart/card/{id}", name="card_infos")
     */
    public function cardInfos($id, SessionInterface $session, CommandRepository $commandRepository)
    {
        $command = $commandRepository->find($id);
        return $this->render('Front/card_cart.html.twig', ['command' => $command]);
    }

    /**
     * @Route("/cart/mail/", name="mail")
     */
    public function mail(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $user_mail = $user->getUserIdentifier();
        $user_true = $userRepository->findBy(['email' => $user_mail]);

        $user_true[0]->setCardName($request->request->get('name'));
        $user_true[0]->setCardNumber($request->request->get('number'));

        $entityManager->persist($user_true[0]);
        $entityManager->flush();

    }

}