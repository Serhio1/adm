<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

class WorkshiftForm extends AbstractType
{

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => ['create'],
            'mode' => 'create',
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] == 'create' || $options['mode'] == 'edit') {
            if ($options['mode'] == 'edit') {
                $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event, $mappingVal) {
                    $data = $event->getData();
                    $form = $event->getForm();
                    //print_r($data);


                });
            }

            $builder
                ->add('name', TextType::class, array(
                        'label' => 'Name:',
                        'error_bubbling' => true,
                    )
                );
            $builder
                ->add('description', TextType::class, array(
                        'label' => 'Description:',
                        'error_bubbling' => true,
                    )
                );
            $builder->add('color', ColorType::class);

            $builder->add('save', SubmitType::class);
        }

        if ($options['mode'] == 'delete') {
            $builder->add('delete', SubmitType::class);
        }
    }
}