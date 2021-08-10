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
        return $this->render('Admin/commands.html.twig', ['commands' => $commands6]);
    }
}