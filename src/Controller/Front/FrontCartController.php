<?php


namespace App\Controller\Front;


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

        if ($user){
            // Si un user est connecté on l'identifie en récupérant son adresse mail
            $user_mail = $user->getUserIdentifier();
            // On cherche dans la base de donnée à quel user il correspond
            $user_true = $userRepository->findBy(['email' => $user_mail]);

            //On récupère les données entrées dans le formulaire
            $name = $_POST['name'];
            $firstname = $_POST['firstname'];
            $email = $_POST['email'];
            $adress = $_POST['adress'];
            $city = $_POST['city'];
            $zipcode = $_POST['zipcode'];
            $user_id = $user_true[0]->getId();


            // On réalise une requete SQL avec PHP : pour mettre à jour les infos passées par l'utilisateur par le user
            // On commence par se connecter au serveur MySQL
            $connexionBdd = mysqli_connect("localhost", "root", "root");
            // On sélectionne la base de données
            $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
            // On écrit la requête SQL sous la forme d'une chaîne de caractères PHP
            $requete = "UPDATE user SET email = '".$email."', adress = '" .$adress. "', city = '".$city."', zipcode = '".$zipcode."',
                        name = '".$name."', firstname = '".$firstname."' WHERE  id = '".$user_id."'";
            // On envoie la requête de puis le script actuel vers la base de données, et récupération du résultat de la requête
            $resultat = mysqli_query($connexionBdd, $requete);
            // On ferme la connexion au serveur MySQL
            mysqli_close($connexionBdd);

            // On récupère toutes les commandes dans un tableau
            $commandall = $commandRepository->findAll();
            // On compte toutes les commandes
            $commandlist = count($commandall);
            // On ajoute + 1 car une commande a été supprimée $number va servir pour number_order de la commande
            $number = $commandlist + 1;
            $date = date('Y-m-d');
            // requête SQL pour enregistrer la nouvelle commande
            $connexionBdd = mysqli_connect("localhost", "root", "root");
            $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
            $requete1 = "INSERT INTO command (user_id, number_order, date, price, zipcode, adress, email, name) VALUES (".$user_id.", '".'Commd-'.$number."', '".$date."', ".$p.", NULL, NULL, NULL, NULL)";
            $resultat1 = mysqli_query($connexionBdd, $requete1);
            mysqli_close($connexionBdd);


            // Pour enregistrer les informations dans la table product_command
            foreach ( $panier as $prod => $quantity){
                $connexionBdd = mysqli_connect("localhost", "root", "root");
                $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
                // On récupère l'id de la dernière command
                $requete = "SELECT MAX(Id) FROM command ";
                $resultat3 = mysqli_query($connexionBdd, $requete);
                mysqli_close($connexionBdd);
                $id = mysqli_fetch_assoc($resultat3);
                $connexionBdd = mysqli_connect("localhost", "root", "root");
                $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
                $product = $productRepository->find($prod);
                $stock_begin = $product->getStock();
                $stock_end = $stock_begin - $quantity ;
                $product->setStock($stock_end);
                $requete2 = "INSERT INTO product_command (product_amount, product_id, command_id) VALUES (". $quantity .", ". $product->getId().", ". $id["MAX(Id)"] .")";
                $resultat2 = mysqli_query($connexionBdd, $requete2);
                mysqli_close($connexionBdd);


                unset($panier[$prod]);
                $session->set('panier', $panier);

                $entityManager->persist($product);
                $entityManager->flush();

            }

            $connexionBdd = mysqli_connect("localhost", "root", "root");
            $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
            $requete = "SELECT MAX(Id) FROM command ";
            $resultat3 = mysqli_query($connexionBdd, $requete);
            $id = mysqli_fetch_assoc($resultat3);
            return $this->redirectToRoute('card_infos', ['id' => $id["MAX(Id)"]]);
            mysqli_close($connexionBdd);
        }else{
            // Si aucun user n'est connecté
            // les informations sont enregistrées dans la table command
            $name = $_POST['name'];
            $firstname = $_POST['firstname'];
            $email = $_POST['email'];
            $adress = $_POST['adress'];
            $city = $_POST['city'];
            $zipcode = $_POST['zipcode'];
            $date = date('Y-m-d');

            //
            $commandall = $commandRepository->findAll();
            $commandlist = count($commandall);
            $number = $commandlist + 1 ;

            $connexionBdd = mysqli_connect("localhost", "root", "root");
            $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
            $requete1 = "INSERT INTO command (user_id, number_order, date, price, zipcode, adress, email, name, city) VALUES 
            (NULL, '".'Commd-'.$number."', '".$date."', ".$p.", '".$zipcode."', '".$adress."', '".$email."', '".$name. " " .$firstname."', '".$city."')";
            $resultat1 = mysqli_query($connexionBdd, $requete1);
            mysqli_close($connexionBdd);



            foreach ( $panier as $prod => $quantity){
                $connexionBdd = mysqli_connect("localhost", "root", "root");
                $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
                $requete = "SELECT MAX(Id) FROM command ";
                $resultat3 = mysqli_query($connexionBdd, $requete);
                mysqli_close($connexionBdd);
                $id = mysqli_fetch_assoc($resultat3);
                $connexionBdd = mysqli_connect("localhost", "root", "root");
                $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
                $product = $productRepository->find($prod);
                $stock_begin = $product->getStock();
                $stock_end = $stock_begin - $quantity ;
                $product->setStock($stock_end);
                $requete2 = "INSERT INTO product_command (product_amount, product_id, command_id) VALUES (". $quantity .", ". $product->getId().", ". $id["MAX(Id)"] .")";
                $resultat2 = mysqli_query($connexionBdd, $requete2);
                mysqli_close($connexionBdd);


                unset($panier[$prod]);
                $session->set('panier', $panier);

                $entityManager->persist($product);
                $entityManager->flush();


            }

            $connexionBdd = mysqli_connect("localhost", "root", "root");
            $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
            $requete = "SELECT MAX(Id) FROM command ";
            $resultat3 = mysqli_query($connexionBdd, $requete);
            $id = mysqli_fetch_assoc($resultat3);
            return $this->redirectToRoute('card_infos', ['id' => $id["MAX(Id)"]]);
            mysqli_close($connexionBdd);
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
            $message = (new \Swift_Message('Nouveau contact'))
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