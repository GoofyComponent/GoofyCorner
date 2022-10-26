<?php

namespace App\Controller;

use App\Entity\User;
// Edit User Form
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManager;

use App\Form\EditUserType;

class UserController extends AbstractController
{
    #[Route('/user/edit', name:'app_user_edit')]
    public function index(UserRepository $userRepository, Request $request,EntityManagerInterface $entityManager): Response
    {



        $user = $this->getUser();

        if (!$user){
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(EditUserType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Redirect
            $user = $form->getData();
            $image = $form->get('image')->getData();
            if ($form->get('image')->getData()!='' && $form->get('image')->getData()!=null){
                $file = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $file
                );
                $user->setImage($file);
            }
            if ($form->get('plainPassword')->getData()!='' && $form->get('plainPassword')->getData()!=null){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            // Save user data and Flush
            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->render('user/index.html.twig', [
            'editForm'=>$form->createView(),
            'user' => $user
        ]);
    }
}

?>