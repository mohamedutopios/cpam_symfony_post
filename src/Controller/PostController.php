<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{

    private function initialize(Request $request): void
    {
        $session = $request->getSession();

        if (!$session->get('posts')) {
            $posts = [
                1 => new Post(1, "Post 1", "Je suis un post 1", new \DateTime('-2 days')),
                2 => new Post(2, "Post 2", "Je suis un post 2", new \DateTime('-1 days'))
            ];
            $session->set('posts', $posts);
        }

    }


    #[Route('/post', name: 'app_post')]
    public function index(Request $request): Response
    {
        $this->initialize($request);
        $session = $request->getSession();
        $posts = $session->get('posts');
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    #[Route("/post/create", name: 'post_create')]
    public function create(Request $request): Response
    {
        $this->initialize($request);
        $session = $request->getSession();

        if ($request->getMethod() == "POST") {
            $posts = $session->get('posts');
            $title = $request->request->get('title');
            $content = $request->request->get('content');
            $newId = count($posts) + 1;
            $newPost = new Post($newId, $title, $content, new \DateTime());

            $posts[$newId] = $newPost;
            $session->set('posts', $posts);

            return $this->redirectToRoute('app_post');
        }

        return $this->render("post/create.html.twig");

    }


}
