<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', EntityType::class, [
                'label' => 'resource.user.role',
                'class' => 'App\Entity\Role',
                'choice_label' => 'name',
                'placeholder' => '_choice',
                'attr' => ['autofocus' => 'autofocus'],
            ])
            ->add('username', TextType::class, [
                'label' => 'resource.user.username',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'first_options' => ['label' => 'resource.user.password'],
                'second_options' => ['label' => 'resource.user.password_confirm'],
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank(['groups' => 'plainPassword']),
                    new Length(['groups' => 'plainPassword', 'min' => 6, 'max' => 16]),
                    new Regex(['groups' => 'plainPassword', 'pattern' => '/[0-9A-Za-z.-_]$/']),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => '_submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate'],
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                $groups = ['role', 'username'];
                if (null !== $data->getPlainPassword() || $data->isNew()) {
                    array_push($groups, 'plainPassword');
                }

                return $groups;
            },
        ]);
    }
}
