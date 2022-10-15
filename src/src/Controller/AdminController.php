<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tag;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }



    #[Route('/admin/create/tag', name: 'app_tag_create')]
    public function createTag(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }
        return $this->render('admin/create_tag.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/all/tag', name: 'app_tag_all')]
    public function allTag(EntityManagerInterface $entityManager): Response
    {
        $tags = $entityManager->getRepository(Tag::class)->findAll();

        return $this->render('admin/all_tag.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/admin/edit/tag', name: 'app_tag_edit', methods: ['GET'])]
    public function editTag(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = $entityManager->getRepository(Tag::class)->find($request->get('id'));
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }
        return $this->render('admin/edit_tag.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/delete/tag', name: 'app_tag_delete', methods: ['GET'])]
    public function deleteTag(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = $entityManager->getRepository(Tag::class)->find($request->get('id'));
        $entityManager->remove($tag);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin');
    }
}
