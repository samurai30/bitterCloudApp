<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LikesController
 * @package App\Controller
 * @Route("/likes")
 */
class LikesController extends AbstractController
{
    /**
     * @Route("/like/{id}", name="likes_like")
     * @param MicroPost $microPost
     * @return JsonResponse
     */
    public function like(MicroPost $micrsoPost)
    {
        $currentUser = $this->getUser();

        if(!$currentUser instanceof User){
            return new JsonResponse([],Response::HTTP_UNAUTHORIZED);
        }

        $micrsoPost->addLikedBy($currentUser);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
           'count' => $micrsoPost->getLikedBy()->count()
        ]);

    }

    /**
     * @param MicroPost $microPost
     * @Route("/unlkie/{id}",name="likes_unlike")
     * @return JsonResponse
     */

    public function unlike(MicroPost $microPost){
        $currentUser = $this->getUser();

        if(!$currentUser instanceof User){
            return new JsonResponse([],Response::HTTP_UNAUTHORIZED);
        }

        $microPost->getLikedBy()->removeElement($currentUser);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $microPost->getLikedBy()->count()
        ]);
    }
}
