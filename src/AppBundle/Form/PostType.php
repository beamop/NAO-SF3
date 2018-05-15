<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type;
use AppBundle\Entity\Post;

class PostType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', Type\TextType::class)
            ->add('content', CKEditorType::class, array('config_name' => 'test_config'))
            ->add('image', Type\FileType::class, array(
                'data_class' => null,
                'required' => false
            ))
            ->add('status', Type\ChoiceType::class, array(
                'choices'  => array(
                    Post::statusToText(Post::DRAFT) => Post::DRAFT,
                    Post::statusToText(Post::PUBLISHED) => Post::PUBLISHED,
                    Post::statusToText(Post::FEATURED) => Post::FEATURED,
                ),
            ))
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Post::class
        ));
    }
}
