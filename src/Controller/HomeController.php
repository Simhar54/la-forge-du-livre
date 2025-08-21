<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\Cache;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Cache(public: true, maxage: 300, smaxage: 300)]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'page_title' => 'La Forge du Livre - Créez vos histoires interactives',
        ]);
    }
}
