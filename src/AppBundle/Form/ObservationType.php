<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ObservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lieu', Type\TextType::class)
            ->add('date', Type\TextType::class)
            ->add('espece', Type\TextType::class)
            ->add('individuals', Type\IntegerType::class)
            ->add('commentaire', Type\TextareaType::class)
        ;
    }
}