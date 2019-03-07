<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

use Symfony\Component\Routing\Annotation\Route;


/**
 * Class MicroPostController
 * @package App\Controller
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{
    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(EntityManagerInterface $entityManager,MicroPostRepository $microPostRepository,FormFactoryInterface $factory,FlashBagInterface $flashBag)
    {
        $this->microPostRepository = $microPostRepository;
        $this->factory = $factory;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    /**
     * @Route("/", name="micro_post_index")
     * @param UserRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(UserRepository $repository)
    {
        $currentUser = $this->getUser();


        $usersTofollow = [];

        if($currentUser instanceof User){
            $posts = $this->microPostRepository->findAllByUsers(
                $currentUser->getFollowing());

                $usersTofollow = count($posts) === 0? $repository->findAllWith5ExecpUser($currentUser): [];


        }else{
            return $this->render('micro_post/index.html.twig', [
                'posts' => $this->microPostRepository->findBy([],['time' => 'DESC']),
                'usersToFollow' => $usersTofollow
            ]);
        }
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts,
            'usersToFollow' => $usersTofollow
        ]);

    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @param MicroPost $mcPost
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('edit',mcPost)",message="Access Denied")
     */
    public function edit(MicroPost $mcPost,Request $request){

        /*if(!$this->denyAccessUnlessGranted('edit',$mcPost)){
            throw new UnauthorizedHttpException();
        }*/
        $form =  $this->factory->create(MicroPostType::class,$mcPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->entityManager->flush();
            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('micro_post/add.html.twig',[
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/add", name="micro_post_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @Security("is_granted('ROLE_USER')")
     */
    public function add(Request $request){
        $user = $this->getUser();

        $microPost = new MicroPost();

        $microPost->setUser($user);
        $form =  $this->factory->create(MicroPostType::class,$microPost);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();
            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('micro_post/add.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}",name="micro_post_delete")
     * @param MicroPost $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('delete',post)",message="Access Denied")
     */
    public function delete(MicroPost $post){

        $this->entityManager->remove($post);
        $this->entityManager->flush();
        $this->flashBag->add('notice', 'Post was deleted');
        return $this->redirectToRoute('micro_post_index');
    }

    /**
     * @Route("/user/{username?}", name="micro_post_user")
     * @param User $userPost
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userPost(User $userPost){

        return $this->render('micro_post/user.html.twig',[
           'posts' => $this->microPostRepository->findBy(
               ['user' => $userPost],
               ['time' => 'DESC']
           ),
            'user' => $userPost
        ]);
    }


    /**
     * @Route("/{id}", name="micro_post_post")
     * @param MicroPost $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post(MicroPost $post)
    {
        return $this->render('micro_post/post.html.twig',[
            'post' => $post
        ]);
    }
}
