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

    // Route pour voir un post spécifique, avec validation que l'id est numérique
    #[Route("/post/{id}", name: "post_show", requirements: ["id" => "\d+"])]
    public function show(Request $request, int $id): Response
    {
        // Assurez-vous que les données sont initialisées
        $this->initialize($request);

        // Accès à la session et récupération des posts
        $session = $request->getSession();
        $posts = $session->get('posts', []);

        // Tentative de récupération du post spécifique par son id
        $post = $posts[$id] ?? null;

        // Si le post n'existe pas, lance une exception de non trouvé
        if (!$post) {
            throw $this->createNotFoundException(sprintf('The post with ID %d was not found.', $id));
        }

        // Rendu de la vue show.html.twig pour afficher le post
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
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

    #[Route("/post/{id}/edit", name: "post_edit")]
    public function edit(Request $request, int $id): Response
    {
        $this->initialize($request);
        $session = $request->getSession();
        $posts = $session->get('posts', []);
        $post = $posts[$id] ?? null;

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        if ($request->isMethod('POST')) {
            $post->title = $request->request->get('title');
            $post->content = $request->request->get('content');

            $posts[$id] = $post;
            $session->set('posts', $posts);

            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
        ]);
    }


    #[Route("/post/{id}/delete", name: "post_delete")]
    public function delete(Request $request, int $id): Response
    {
        $this->initialize($request);

        $session = $request->getSession();
        $posts = $session->get('posts', []);

        if (isset($posts[$id])) {
            unset($posts[$id]);
            $session->set('posts', $posts);
        }

        return $this->redirectToRoute('app_post');
    }


}
