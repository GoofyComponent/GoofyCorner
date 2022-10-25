<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Post;
use App\Repository\TagRepository;
use App\Repository\PostRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArchiveController extends AbstractController
{
    // deux paramètre non obligatoire : tag et search
    #[Route('/search', name: 'app_search', methods: ['GET'])]

    public function index(Request $request, TagRepository $tagRepository, PostRepository $postRepository): Response
    {
        

        $search = $request->query->get('search');
        $tag = $request->query->get('tag');

        $post = $postRepository->findByTagAndName($tag, $search);
        dd($post);
        return $this->render('archive/index.html.twig', [
            'controller_name' => 'ArchiveController',

        ]);
    
    
    }
}