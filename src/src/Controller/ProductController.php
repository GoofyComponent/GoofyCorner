<?php

namespace App\Controller;

use App\Form\PostType;
use App\Entity\Post;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    #[Route('/product/new', name: 'app_product_new')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            #Add the createdTime to the post
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setModifiedAt(new \DateTimeImmutable());
            $post->setUser($this->getUser());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_show', ['id' => $post->getId()]);
        }
        elseif ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Please fill in all the fields');
        }
        else {
            return $this->render('product/create.html.twig',
            [
                'createProductForm' => $form->createView(),
            ]
        );
        }

        
    }

    #[Route('/product/{id}', name: 'app_product_show')]
    public function show($id, Request $request, EntityManagerInterface $entityManager): Response
    {

        $post = $entityManager->getRepository(Post::class)->find($id);

        if(!$post) {
            return $this->redirectToRoute('app_404');
        }

        return $this->render('product/show.html.twig', [
            'id' => $id,
            'product' => $post,
        ]);
    }

    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
}