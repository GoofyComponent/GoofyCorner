<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use App\Repository\TagRepository;
#[AsTwigComponent('header')]
class HeaderComponent
{
    // on récupère les tags
    public function __construct(private TagRepository $tagRepository)
    {
    }


    public function getTags(): array
    {
        return $this->tagRepository->findAll();
    }
}