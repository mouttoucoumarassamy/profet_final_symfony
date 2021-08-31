<?php


namespace App\Controller\Front;


use App\Entity\Command;
use App\Repository\CommandRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
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
    public function infoCommand(SessionInterface $session,
                             ProductRepository $productRepository,
                             UserRepository $userRepository,
                             Request $request,
                             EntityManagerInterface $entityManager,
                             CommandRepository $commandRepository
                             )
    {
        // On récupère le panier qui est enregistré dans la session
        $panier = $session->get('panier', []);
        $p = 0;

        // Pour chaque produit on cherche son prix et on multiplie par la quantité et on l'ajoute
        // à la variable p pour avoir le prix total du panier
        foreach ( $panier as $prod => $quantity){
            $product = $productRepository->find($prod);
            $price_product = $product->getPrice();
            $p = $p + $price_product*$quantity;
        }


        // On récupère le user connecté
        $user = $this->getUser();

        // On récupère toutes les commandes dans un tableau
        $commandall = $commandRepository->findAll();
        // On compte toutes les commandes
        $commandlist = count($commandall);
        // On ajoute + 1 car une commande a été supprimée $number va servir pour number_order de la commande
        $number = $commandlist + 1;
        $date = new \DateTime("NOW") ;

        //On attribue les données à une nouvelle commande
        $command = new Command();
        $command->setNumberOrder('Commd-'.$number);
        $command->setDate($date);
        $command->setPrice($p);

        // On enregistre dans la base de données
        $entityManager->persist($command);
        $entityManager->flush();

        $id = $command->getId();

        // Pour enregistrer les informations dans la table product_command
        foreach ( $panier as $prod => $quantity) {
            // On réalise une requête SQL avec une chaîne de caractères PHP
            // On se connecte au serveur MySQL
            $connexionBdd = mysqli_connect("localhost", "root", "root");
            // ON sélectionne la base de données
            $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
            // On change le stocke du produit
            $product = $productRepository->find($prod);
            $stock_begin = $product->getStock();
            $stock_end = $stock_begin - $quantity;
            $product->setStock($stock_end);
            // On écrit la requête SQL sous forme de chaîne de caractères PHP
            $requete = "INSERT INTO product_command (product_amount, product_id, command_id) VALUES (" . $quantity . ", " . $product->getId() . ", " . $id . ")";
            // On envoie de la requête depuis le script actuel vers la base de données et on récupère le résultat de la requête
            $resultat = mysqli_query($connexionBdd, $requete);
            // On ferme la connexion au serveur MySQL
            mysqli_close($connexionBdd);

            // On vide le panier
            unset($panier[$prod]);
            $session->set('panier', $panier);

            // On enregistre dans la base de données les changements du produit
            $entityManager->persist($product);
            $entityManager->flush();

        }

        if ($user){
            // Si un user est connecté on l'identifie en récupérant son adresse mail
            $user_mail = $user->getUserIdentifier();
            // On cherche dans la base de donnée à quel user il correspond
            $user_true = $userRepository->findBy(['email' => $user_mail]);



             // On récupère les données du formulaire
            $name = $request->request->get('name');
            $firstname = $request->request->get('firstname');
            $email = $request->request->get('email');
            $adress = $request->request->get('adress');
            $city = $request->request->get('city');
            $zipcode = $request->request->get('zipcode');

            //On attribue au user les données
            $user_true[0]->setEmail($email);$user_true[0]->setName($name);
            $user_true[0]->setFirstname($firstname);
            $user_true[0]->setAdress($adress);$user_true[0]->setCity($city);
            $user_true[0]->setZipcode($zipcode);

            // On enregistre dans la base de données
            $entityManager->persist($user_true[0]);
            $entityManager->flush();

            // on attribue le user à la command
            $command->setUser($user_true[0]);

            // On enregistre dans la base de données
            $entityManager->persist($command);
            $entityManager->flush();

        }else{
            // Si aucun user n'est connecté
            // les informations sont enregistrées dans la table command

            // Récupération des données du formualire
            $name = $request->request->get('name');
            $firstname = $request->request->get('firstname');
            $email = $request->request->get('email');
            $adress = $request->request->get('adress');
            $city = $request->request->get('city');
            $zipcode = $request->request->get('zipcode');



            //On attribue les données à la commande
            $command->setName($name . " " . $firstname);
            $command->setEmail($email);
            $command->setAdress($adress);
            $command->setCity($city);
            $command->setZipcode($zipcode);

            // On enregistre dans la base de données
            $entityManager->persist($command);
            $entityManager->flush();

        }

        return $this->redirectToRoute('card_infos', ['id' => $id]);

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
    public function mail(UserRepository $userRepository,
                         Request $request,
                         EntityManagerInterface $entityManager,
                         CommandRepository $commandRepository,
                            \Swift_Mailer $mailer)
    {
        // Méthode d'envoi de mail
        $user = $this->getUser();
        if ($user){
            // Si un user est connecté on récupère ses informations
            $user_mail = $user->getUserIdentifier();
            $user_true = $userRepository->findBy(['email' => $user_mail]);
            $user_true[0]->setCardName($request->request->get('name'));
            $user_true[0]->setCardNumber($request->request->get('number'));
            $entityManager->persist($user_true[0]);
            $entityManager->flush();
            $command = $commandRepository->findAll();
            $count = count($command);
            $command_one = $commandRepository->find($count + 1);
            // Envoi du message
            $message = (new \Swift_Message('Nouvelle commande'))
                // On attribue l'expéditeur
                ->setFrom('superediscount@smail.com')

                // On attribue le destinataire
                ->setTo($user_true[0]->getEmail())

                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'Front/mail.html.twig', ['command' => $command_one]
                    ),
                    'text/html'
                );

            // On envoie le message
            $mailer->send($message);
        }else{
            // On fait le même chose si aucun user n'est connecté
            // les infos sont récupérées via l'entité command
            $command = $commandRepository->findAll();
            $count = count($command);
            $command_one = $commandRepository->find($count + 1 );

            $mail = $command_one->getEmail();

            $message = (new \Swift_Message('Nouveau contact'))
                // On attribue l'expéditeur
                ->setFrom('superediscount@smail.com')

                // On attribue le destinataire
                ->setTo($mail)

                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'Front/mail.html.twig', ['command' => $command_one]
                    ),
                    'text/html'
                );

            $mailer->send($message);
        }
        return $this->redirectToRoute('front_home');
    }

}