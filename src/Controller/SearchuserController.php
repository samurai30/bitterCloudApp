<?php

namespace App\Controller;


use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
// For annotations
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class SearchuserController extends AbstractController
{

    public function searchBar(){
        $form = $this->createFormBuilder(null,[
            'attr' => ['autocomplete' => 'off']
        ])
            ->setAction($this->generateUrl('handle_request'))
            ->add("query", TextType::class,[
                'attr' => [
                    'placeholder' => 'Search Users',


                ],
                'label' => false
            ])
            ->getForm();
        return $this->render('micro_post/search.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/handleSearch/{_query?}", name="handle_request", methods={"POST","GET"})
     * @param Request $request
     * @param $_query
     * @return JsonResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @Security("is_granted('ROLE_USER')")
     */
    public function handleSearchRequest(Request $request,$_query){
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository(User::class)->findByName($_query);
        $encoders = [
            new JsonEncoder()
        ];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer],$encoders);

        $json = $serializer->serialize($users, 'json',[
            'groups' => 'group1'
        ]);
        return new JsonResponse($json,200,[],true);


    }
}
