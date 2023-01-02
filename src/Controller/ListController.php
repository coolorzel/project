<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    private $postsRepository;
    public function __construct(PostsRepository $postsRepository)
    {
        $this->postsRepository = $postsRepository;
    }
    #[Route('/lista', name: 'app_list')]
    public function index(): Response
    {
        $posts = $this->postsRepository->findAll();
        return $this->render('list/index.html.twig', ['posts' => $posts]);
    }
}
