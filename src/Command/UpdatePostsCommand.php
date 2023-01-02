<?php

namespace App\Command;

use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttplugClient;
use GuzzleHttp\Client;

#[AsCommand(
    name: 'app:update-posts',
    description: 'The execution of the command will download data from the REST API and save it to the database.',
)]
class UpdatePostsCommand extends Command
{
    private $entityManager;
    private $countCreate = 0;
    private $countUpdate = 0;
    private $countDelete = 0;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('posts', InputArgument::OPTIONAL, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $inputPostsUrl = 'https://jsonplaceholder.typicode.com/posts';
        $inputUsersUrl = 'https://jsonplaceholder.typicode.com/users/';
        $httpClient = new Client();
        $allTitle = [];
        $requestPost = $httpClient->request('GET', $inputPostsUrl);
        $dataPost = json_decode($requestPost->getBody(), true);
        $query = $this->entityManager->createQuery('SELECT p FROM App\Entity\Posts p WHERE p.title = :title');
        foreach ($dataPost as $record)
        {
            $allTitle[$record['title']] = $record['title'];
            $query->setParameter('title', $record['title']);
            $posts = $query->getResult();
            if(count($posts) == 0)
            {
                $data[0] = $record['title'];
                $data[1] = $record['body'];
                $requestUser = $httpClient->request('GET', $inputUsersUrl.$record['userId']);
                $dataUser = json_decode($requestUser->getBody(), true);
                $data[2] = $dataUser['name'];
                $this->createPost($data);
                $this->countCreate++;
            }
            else
            {
                $query2 = $this->entityManager->createQuery('SELECT p FROM App\Entity\Posts p WHERE p.title = :title');
                $query2->setParameter('title', $record['title']);
                $response = $query2->getResult();
                $requestUser = $httpClient->request('GET', $inputUsersUrl.$record['userId']);
                $dataUser = json_decode($requestUser->getBody(), true);
                //dd($record);
                if($record['body'] != $response[0]->getBody() || $dataUser['name'] != $response[0]->getName())
                {
                    $title = $record['title'];
                    $body = $record['body'];
                    $name = $dataUser['name'];
                    $id = $response[0]->getId();
                    $updatePost = $this->updatePost($id, $title, $body, $name);
                    $this->countUpdate++;
                }
            }
        }
        $query3 = $this->entityManager->createQuery('SELECT p FROM App\Entity\Posts p');
        //$query3->setParameter('title', $record['title']);
        $result = $query3->getResult();
        foreach ($result as $item)
        {
            if (!in_array($item->getTitle(), $allTitle))
            {
                $this->deletePost($item->getId(), $item->getTitle());
                $this->countDelete++;
            }
        }
        $io->success($this->countCreate.' new posts, and '.$this->countUpdate.' update old posts, and '.$this->countDelete.' delete bad posts.');

        return Command::SUCCESS;
        //dd($this->countCreate.' new posts, and '.$this->countUpdate.' update old posts, and '.$this->countDelete.' delete bad posts.');


        /*$io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
        */
    }

    public function createPost(array $data)
    {
        $post = new Posts();
        $post->setTitle($data[0]);
        $post->setBody($data[1]);
        $post->setName($data[2]);

        $this->entityManager->persist($post);
        $this->entityManager->flush();
        return $post;
    }

    public function updatePost(int $id, string $title, string $body, string $name)
    {
        $post = $this->entityManager->getRepository(Posts::class)->find($id);

        if (!$post)
        {
            throw new Exception('Do not find post.');
        }
        $post->setBody($body);
        $post->setName($name);
        $this->entityManager->flush($post);
        return true;
    }

    public function deletePost(int $id, string $title)
    {
        $post = $this->entityManager->getRepository(Posts::class)->find($id);
        if (!$post)
        {
            throw new Exception('Do not find post.');
        }
        $this->entityManager->remove($post);
        $this->entityManager->flush();
        return true;
    }
}
