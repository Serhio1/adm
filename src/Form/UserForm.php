<?php

namespace App\Form;

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

class UserForm extends AbstractType
{

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User',
            'validation_groups' => ['create'],
            'selfEdit' => false,
            'issuedUserId' => null,
            'mode' => 'create',
            'subRoles' => null
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] == 'create' || $options['mode'] == 'edit') {
            $builder
                ->add('username', TextType::class, array(
                        'label' => 'Login:',
                        'error_bubbling' => true,
                    )
                )
                ->add('email', EmailType::class, array(
                        'label' => 'Email:',
                        'error_bubbling' => true,
                    )
                );

            if (!$options['selfEdit']) {
                $builder->
                add('isActive', CheckboxType::class, array(
                        'label' => 'Active',
                        'attr' => array(
                            'checked' => true,
                        )
                    )
                );
                $options['subRoles'] = array_combine(
                    $options['subRoles'],
                    $options['subRoles']
                );
                $builder->add('roles', ChoiceType::class, array(
                    'label' => 'Roles',
                    'choices' => $options['subRoles'],
                    'multiple' => true,
                ));
            }
            if ($options['mode'] == 'create') {
                $builder->add('password', PasswordType::class, array(
                        'label' => 'Password:',
                        'error_bubbling' => true,
                    )
                );

            }
            if ($options['mode'] == 'edit') {
                $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    $user = $event->getData();
                    $form = $event->getForm();

                    // checks if the object is "new"
                    if (!$user || null === $user->getId()) {

                    }
                });
            }

            $builder->add('save', SubmitType::class);
        }

        if ($options['mode'] == 'resetPassword') {
            $builder->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ));

            $builder->add('reset', SubmitType::class);
        }


        if ($options['mode'] == 'delete') {
            $builder->add('delete', SubmitType::class);
        }
    }
}