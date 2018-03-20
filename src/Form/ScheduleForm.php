<?php

namespace App\Form;

use App\Form\Type\TimetableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\Type\ShippingType;

class ScheduleForm extends AbstractType
{

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => ['create'],
            'mode' => 'create',
            'flags' => array(),
            'entities' => null,
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] == 'create' || $options['mode'] == 'edit') {
            $mappingOptions = array(
                'variants' => array(
                    'mon' => null,
                    'tue' => null,
                    'wed' => null,
                    'thu' => null,
                    'fri' => null,
                    'sat' => null,
                    'sun' => null,
                ),
                'flags' => $options['flags']
                /*'flags' => array(
                    array(
                        'attr' => array(
                            'style' => 'background-color:transparent'
                        ),
                        'name' => 'null',
                    ),
                    array(
                        'attr' => array(
                            'style' => 'background-color:blue',
                        ),
                        'name' => 'day',
                    ),
                    array(
                        'attr' => array(
                            'style' => 'background-color:red',
                            'title' => '16:00 - 24:00'
                        ),
                        'name' => 'night',
                    ),
                    array(
                        'attr' => array(
                            'style' => 'background-color:brown',
                        ),
                        'name' => 'all',
                    ),
                )*/
            );

            if ($options['mode'] == 'edit') {
                /*$mappingOptions['controls']['add']['enabled'] = false;
                $mappingOptions['controls']['remove']['enabled'] = false;
                $mappingOptions['controls']['save']['enabled'] = true;
                $mappingOptions['controls']['save']['text'] = 'save';*/
                $entities = $options['entities'];
                foreach ($entities as $key => $entity) {
                    $mappingArr[$entity->getId()] = json_decode($entity->getMapping());
                }
                $mappingVal = json_encode((object)$mappingArr);
                $mappingOptions['attr']['value'] = $mappingVal;
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

            /*$builder->add('mapping', CollectionType::class, array(
                'entry_type' => TextType::class,
                'entry_options' => array(
                        'attr' => array(

                        ),
                    'label' => false,

                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'allow-add allow-remove',
                ),
                'data' => array(
                    '0000000',
                    '0000000',
                    '0000000',
                    '0000000',
                    '0000000',
                    '0000000',
                ),
            ));*/

            /*$builder->add('mapping', TextType::class, array(
                'attr' => array(
                  'class' => 'schedule',
                ),
            ));*/



            $builder->add('mapping', TimetableType::class, $mappingOptions);




            $builder->add('save', SubmitType::class);
        }

        if ($options['mode'] == 'delete') {
            $builder->add('delete', SubmitType::class);
        }
    }
}