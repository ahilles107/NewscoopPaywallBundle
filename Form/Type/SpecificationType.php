<?php

namespace Newscoop\PaywallBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SpecificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('publication', 'integer', array(
            'required' => true
        ))
        ->add('issue', 'integer', array(
            'required' => false
        ))
        ->add('section', 'integer', array(
            'required' => false
        ))
        ->add('article', 'integer', array(
            'required' => false
        ));
    }

    public function getName()
    {
        return 'specificationForm';
    }
}