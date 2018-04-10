<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;

class MailerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', Type\TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Nom complet'
                ),
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Merci d\'entrer un nom'
                    )),
                )
            ))
            ->add('sujet', Type\TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Sujet'
                ),
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Merci d\'entrer un sujet'
                    )),
                )
            ))
            ->add('email', Type\EmailType::class, array(
                'attr' => array(
                    'placeholder' => 'E-mail'
                ),
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Merci d\'ajouter une adresse email'
                    )),
                    new Constraints\Email(array(
                        'message' => 'Votre email n\'est pas valide'
                    )),
                )
            ))
            ->add('message', Type\TextareaType::class, array(
                'attr' => array(
                    'placeholder' => 'Message'
                ),
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Merci d\'ajouter un message'
                    )),
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true
        ));
    }

    public function getName()
    {
        return 'contact_form';
    }
}