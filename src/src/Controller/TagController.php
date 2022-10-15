<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Tag;
use App\Form\TagType;

class TagController extends AbstractController
{

    #[Route('/tag/create', name: 'app_tag_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd($tag);
        }
        return $this->redirectToRoute('app_admin');
    }
}
