<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class,[
                'attr' => ['placeholder' => 'username'],
                'label' => false
            ])
            ->add('email',EmailType::class,[
                'attr' => ['placeholder' => 'Email'],
                'label' => false
            ])
            ->add('plainPassword',RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options' => ['attr' =>  ['placeholder' => 'password'],
                    'label' => false],
                'second_options' => ['attr' => ['placeholder' => 'confirm password']
                , 'label' => false]
            ])
            ->add('fullName', TextType::class)
            ->add('termsAgreed', CheckboxType::class,[
                'mapped' => false,
                'constraints' => new IsTrue(),
                'label' => 'I agree to the terms & conditions'
            ])
            ->add('Register', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
