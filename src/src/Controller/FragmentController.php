<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FragmentController extends AbstractController
{
    public function header(): Response
    {
        return $this->render('fragment/_header.html.twig', [
        ]);
    }
}