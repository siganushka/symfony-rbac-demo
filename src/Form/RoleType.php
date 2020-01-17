<?php

namespace App\Form;

use App\Entity\Role;
use Siganushka\RBACBundle\Form\Type\NodeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'resource.role.name',
                'attr' => ['autofocus' => 'autofocus'],
            ])
            ->add('nodes', NodeType::class, [
                'label' => 'resource.role.nodes',
            ])
            ->add('submit', SubmitType::class, [
                'label' => '_submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
