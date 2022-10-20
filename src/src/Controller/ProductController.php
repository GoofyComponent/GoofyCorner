<?php

namespace App\Controller;

use App\Form\PostType;
use App\Entity\Post;
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

        if($post->getUser() === $this->getUser()){
            $isOwner = true;
        }

        #Retreive all data about the user who posted the product
        $user = $post->getUser();
        $userImage = $user->getImage();
        $username = $user->getUsername();
        $userFirstName = $user->getFirstName();
        $user = $user->getEmail();

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