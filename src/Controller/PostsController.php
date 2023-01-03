<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class PostsController extends AbstractController
{
    #[Route('/posts', methods: 'GET', name: 'app_posts')]
    public function index(): Response
    {
        return $this->render('posts/index.html.twig');
    }
}
