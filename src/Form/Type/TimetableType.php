<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;

class TimetableType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr' => array(
                //'value' =>
            ),
            'variants' => array(
                'mon' => null,
                'tue' => null,
                'wed' => null,
                'thu' => null,
                'fri' => null,
                'sat' => null,
                'sun' => null,
            ),
            'flags' => array(
                'd',
                'n',
                'a',
            ),
            'controls' => array(
                'add' => array(
                    'text' => '+',
                    'enabled' => true,
                ),
                'remove' => array(
                    'text' => "&mdash;",
                    'enabled' => true,
                ),
                'save' => array(
                    'text' => 'save',
                    'enabled' => true,
                ),
            ),

        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['variants'] = $options['variants'];
        $view->vars['flags'] = $options['flags'];
        $view->vars['controls'] = $options['controls'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // When empty_data is explicitly set to an empty string,
        // a string should always be returned when NULL is submitted
        // This gives more control and thus helps preventing some issues
        // with PHP 7 which allows type hinting strings in functions
        // See https://github.com/symfony/symfony/issues/5906#issuecomment-203189375
        if ('' === $options['empty_data']) {
            $builder->addViewTransformer($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timetable';
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data)
    {
        // Model data should not be transformed
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        return null === $data ? '' : $data;
    }

    public function getParent()
    {
        return TextType::class;
    }
}