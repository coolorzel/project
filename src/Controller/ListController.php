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
    private $entityManager;

    public function __construct(PostsRepository $postsRepository, EntityManagerInterface $entityManager)
    {
        $this->postsRepository = $postsRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/lista', name: 'app_list')]
    public function index(): Response
    {
        $posts = $this->postsRepository->findAll();
        return $this->render('list/index.html.twig', ['posts' => $posts]);
    }

    #[Route('/lista/delete/{id}', methods: ['GET', 'DELETE'], name: 'app_list_delete')]
    public function delete($id): Response
    {
        $post = $this->postsRepository->find($id);
        $this->entityManager->remove($post);
        $this->entityManager->flush();
        $this->addFlash('success_delete', 'Success delete post id: '.$id);

        return $this->redirectToRoute('app_list');
    }
}
