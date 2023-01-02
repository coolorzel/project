<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    #[Route('/movies/{name}', name: 'app_movies')]
    public function index($name): Response
    {
        return $this->json([
            'message' => $name,
            'path' => 'src/Controller/MoviesController.php',
        ]);
        /*return $this->render('movies/index.html.twig', [
            'controller_name' => 'MoviesController',
        ]);*/
    }
}
