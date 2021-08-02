<?php


namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FrontUserController extends AbstractController
{
    /**
     * @Route("/user/add", name="log_add")
     */
    public function addUser(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Request $request, UserPasswordEncoderInterface $encoder)
    {

        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){
            $user->setRoles(["ROLE_USER"]);
            $user->setDate(new \DateTime("NOW"));
            $plainPassword = $userForm->get('password')->getData();
            $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('front_home');
        }


        return $this->render('front/useradd.html.twig', ['userForm' => $userForm->createView()]);

    }

}