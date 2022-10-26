<?php

namespace App\Controller;

use App\Form\PostType;
use App\Form\QuestionType;
use App\Form\ReponseType;
use App\Entity\Post;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class ProductController extends AbstractController
{

    #[Route('/product/new', name: 'app_product_new')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        #Check if user is logged in
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            #Add the createdTime to the post
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setModifiedAt(new \DateTimeImmutable());
            $post->setUser($this->getUser());

            $images = $form->get('images')->getData();
            $imagesArray = [];
            foreach ($images as $image) {
                $file = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $file
                );
                // on ajoute au tableau des images setImages
                $imagesArray[] = $file;
            }
            $post->setImages($imagesArray);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_show', ['id' => $post->getId()]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Please fill in all the fields');
        } else {
            return $this->render(
                'product/create.html.twig',
                [
                    'createProductForm' => $form->createView(),
                ]
            );
        }
    }
   
    #[Route('/product/newreponse', name: 'app_reponse_new', methods: ['GET'])]
    public function addreponse(Request $request,EntityManagerInterface $entityManager): Response
    {
        $reponse = new Reponse();

        $questionID = $request->query->get('question_id');
        $question = $entityManager->getRepository(Question::class)->find($questionID);
        if (!$question) {
            throw $this->createNotFoundException(
                'No question found for id ' . $questionID
            );
        }
        $post = $question->getPost();
        $user = $this->getUser();
        $reponse->setQuestion($question);
        $reponse->setUser($user);
        $reponse->setContent($request->query->get('reponse'));
        if(!$request->query->get('reponse')){
            throw $this->createNotFoundException(
                'No reponse found for id ' . $questionID
            );
        }
        $entityManager->persist($reponse);
        $entityManager->flush();

        return $this->redirectToRoute('app_product_show', ['id' => $post->getId()]);
            
    }

    

    #[Route('/product/{id}', name: 'app_product_show')]
    public function show($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $isOwner = false;

        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post) {
            return $this->redirectToRoute('app_404');
        }

        $postCreation = $post->getCreatedAt()->format('d-m-Y H:i:s');
        $postModified = $post->getModifiedAt()->format('d-m-Y H:i:s');

        if(!$post) {
            return $this->redirectToRoute('app_404');
        }

        #Retreive all data about the user who posted the product
        $user = $post->getUser();
        $userImage = $user->getImage();
        $username = $user->getUsername();
        $userFirstName = $user->getFirstName();
        $user = $user->getEmail();
        $question = new Question();
        $reponse = new Reponse();

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        $formReponse = $this->createForm(ReponseType::class, $reponse);
        $formReponse->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
            $question = $form->getData();
            $question->setUser($this->getUser());
            $question->setPost($post);

            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_show', ['id' => $post->getId()]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Please fill in all the fields');
        }
        
        
        if ($formReponse->isSubmitted() && $formReponse->isValid() && $this->getUser()) {
            $reponse = $formReponse->getData();
            $reponse->setUser($this->getUser());
            $questionID = $request->request->get('question_id');
            $question = $entityManager->getRepository(Question::class)->find($questionID);
            $reponse->addQuestion($question);

            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_show', ['id' => $question->getId()]);
        } elseif ($form->isSubmitted() && !$formReponse->isValid()) {
            $this->addFlash('error', 'Please fill in all the fields');
        }
        return $this->render('product/show.html.twig', [
            
            'id' => $id,
            'product' => $post,
            'created_at' => $postCreation,
            'modified_at' => $postModified,
            'seller' => [
                'firstName' => $userFirstName,
                'email' => $user,
                'image' => $userImage,
                'username' => $username,
                'isOwner' => $isOwner
            ],
            'addQuestion' => $form->createView(),
            'formReponse' => $formReponse->createView()

        ]);
    }

   

    #[Route('/product/{id}/edit', name: 'app_product_edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $post = $entityManager->getRepository(Post::class)->find($id);

        if($post->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_404');
        }
        
        if (!$post) {
            return $this->redirectToRoute('app_404');
        }

        
        
        $oldImages = $post->getImages();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        #Get the data of the product seller
        $user = $post->getUser();
        $userImage = $user->getImage();
        $username = $user->getUsername();
        $userFirstName = $user->getFirstName();
        $user = $user->getEmail();

        if ($form->isSubmitted() && $form->isValid()) {

            //Get the removedImage input
            $removedImage = $request->request->get('removedImage');
            //Remove the [ ] from the string
            $removedImage = str_replace('[', '', $removedImage);
            $removedImage = str_replace(']', '', $removedImage);
            //Convert the string to an array
            $removedImage = explode(',', $removedImage);

            $post = $form->getData();

            $post->setModifiedAt(new \DateTimeImmutable());

            $imagesArray = $oldImages;

            //If removedImage is not empty
            if (!empty($removedImage)) {
                if($removedImage[0]===""){
                    $removedImage = [];
                }
                //Remove the images that were removed
                foreach ($removedImage as $image) {
                    if ($image != "0") {
                        //Remove the image from the server
                        $filesystem = new Filesystem();
                        $filesystem->remove($this->getParameter('images_directory') . '/' . $imagesArray[$image]);
                        unset($imagesArray[$image]);
                    }
                }
            }
        
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $file = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $file
                );
                $imagesArray[] = $file;
            }
            
            $post->setImages($imagesArray);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_show', ['id' => $post->getId()]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Please fill in all the fields');
        } else {
            return $this->render(
                'product/edit.html.twig',
                [
                    'editProductForm' => $form->createView(),
                    'product' => $post,
                    'seller' => [
                        'firstName' => $userFirstName,
                        'email' => $user,
                        'image' => $userImage,
                        'username' => $username,
                    ],
                ]
            );
        }
    }
}