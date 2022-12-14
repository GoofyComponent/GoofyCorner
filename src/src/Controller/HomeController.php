<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Tag;
use App\Repository\TagRepository;

class HomeController extends AbstractController
{

    #[Route('/404', name: 'app_404')]
    public function quatreCentQuatre(): Response
    {
        return $this->render('home/404.html.twig', [
            'controller_name' => '404',
        ]);
    }

    #[Route('/', name: 'app_home')]
    public function index(TagRepository $tagRepository) : Response
    {
        $tags = $tagRepository->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tags' => $tags
        ]);
    }
}