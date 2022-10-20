<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    #[Route('/user/{id}', name:'app_user', methods:['GET'])]
    public function index($id, UserRepository $userRepository): Response
    {
        $userFetch = $userRepository->find($id);
        $user = $this->getUser();

        if($userFetch->getId() == $user->getId()) {
            $canEdit = true;
        } else {
            $canEdit = false;
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'canEdit' => $canEdit,
        ]);
    }
}

?>