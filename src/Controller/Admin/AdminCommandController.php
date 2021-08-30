<?php

namespace App\Controller\Admin;

use App\Repository\CommandRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommandController extends AbstractController
{

    /**
     * @Route("/admin/commands/", name="list_commands")
     */
    public function listCommands(CommandRepository $commandRepository, PaginatorInterface $paginator, Request $request)
    {
        $commands = $commandRepository->findAll();
        $commands6 = $paginator->paginate($commands, $request->query->getInt('page', 1), 6);

        $connexionBdd = mysqli_connect("localhost", "root", "root");
        $selectionBdd = mysqli_select_db($connexionBdd, "project_final_piscine");
        $requete = "SELECT * FROM product_command";
        $resultat = mysqli_query($connexionBdd, $requete);
        $card = [];
        while ($line = mysqli_fetch_assoc($resultat)){
            $card[] = $line;
        }
        return $this->render('Admin/commands.html.twig', ['commands' => $commands6, 'cards' => $card]);
        mysqli_close($connexionBdd);
    }
}