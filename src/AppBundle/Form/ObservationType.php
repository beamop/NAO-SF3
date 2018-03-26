<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ObservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adresse')
            ->add('date', Type\DateType::class)
            ->add('espece', Type\TextType::class)
            ->add('individuals', Type\IntegerType::class)
            ->add('commentaire', Type\TextareaType::class)
        ;

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $data = $event->getForm()->getData();

                $adresse = $data->getAdresse();
                $apikey = 'AIzaSyB9aZefiyGfTLfqolpIOMny-2Qa3ssDQFE';
                $geocode = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($adresse).'&key='.$apikey);
                $output = json_decode($geocode);
                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;

                $data->setLatitude($latitude);
                $data->setLongitude($longitude);
            }
        );
    }
}