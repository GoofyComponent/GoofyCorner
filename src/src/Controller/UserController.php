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
// Published Posts
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\EditUserType;

class UserController extends AbstractController
{

    #[Route('/user', name: 'user_own')]
    public function display(EntityManagerInterface $entityManager, ): Response
    {

        $user = $this->getUser();

        if (!$user){
            return $this->redirectToRoute('app_home');
        }

        //Get all posts from the user 
        $posts = $user->getPost();

        return $this->render('user/show.html.twig', [
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'lastname' => $user->getLastname(),
                'firstname' => $user->getFirstname(),
                'adresse' => $user->getAdresse(),
                'email' => $user->getEmail(),
                'image' => $user->getImage(),
            ],
            'posts' => $posts,
            'type' => 'own',
        ]);
    }

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

            return $this->redirectToRoute('user_own');
        }
        
        return $this->render('user/index.html.twig', [
            'editForm'=>$form->createView(),
            'user' => $user,
            'not_header' => true
        ]);
    }

    #[Route('/user/{id}', name: 'user_other')]
    public function displayOtherUser($id, User $user, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser()){
            if($this->getUser()->getId() == $id){
                return $this->redirectToRoute('user_own');
            }
        }
        //Find user by id
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user){
            return $this->redirectToRoute('app_home');
        }

        //Get all posts from the user 
        $posts = $user->getPost();

        return $this->render('user/show.html.twig', [
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'lastname' => $user->getLastname(),
                'firstname' => $user->getFirstname(),
                'adresse' => $user->getAdresse(),
                'email' => $user->getEmail(),
                'image' => $user->getImage(),
            ],
            'posts' => $posts,
            'type' => 'other'
        ]);
    }
}

?>