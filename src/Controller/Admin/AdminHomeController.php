<?php


namespace App\Controller\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class AdminHomeController extends AbstractController
{
    /**
     * @Route("/admin/", name="admin_home")
     */
    public function home()
    {
        return $this->render("admin/home.html.twig");
    }

}