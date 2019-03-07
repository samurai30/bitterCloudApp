<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class BlogController
 * @package App\Controller
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(\Twig_Environment $twig,SessionInterface $session,
RouterInterface $router)
    {
        $this->twig = $twig;
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @Route("/", name="blog_index")
     */
    public function index()
    {
        $html = $this->twig->render('blog/index.html.twig',
            [
                'posts' => $this->session->get('posts')
            ]);

        return new Response($html);
    }
    /**
     * @Route("/add",name="blog_add")
     */
    public function add(){
        $posts = $this->session->get('posts');
        $posts[uniqid()] = [
            'title' => 'Random Title'.rand(1,500),
            'text' => 'Some Random text '.rand(1,500),
            'date' => new \DateTime(),

        ];
        $this->session->set('posts', $posts);
        return new RedirectResponse($this->router->generate('blog_index'));

    }

    /**
     * @Route("/show/{id}", name="blog_show")
     * @param $id
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function show($id){
        $posts = $this->session->get('posts');
        if(!$posts|| !isset($posts[$id])){
            throw new NotFoundHttpException('Post not found');

        }

        $html = $this->twig->render('blog/post.html.twig',[
            'id' => $id,
            'post' => $posts[$id],
        ]);
        return new Response($html);
    }
}
