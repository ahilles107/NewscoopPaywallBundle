<?php

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\PaywallBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SubscriptionConfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', null, array(
            'label' => 'paywall.step1.form.label.name',
            'error_bubbling' => true,
            'invalid_message' => 'paywall.step1.form.error.name',
        ))
        ->add('description', 'textarea', array(
            'label' => 'paywall.step1.form.label.description',
            'required' => false,
            'error_bubbling' => true,
        ))
        ->add('type', 'choice', array(
            'label' => 'paywall.step1.form.label.type',
            'choices' => array(
                'publication' => 'paywall.step1.form.select.type.publication',
                'issue' => 'paywall.step1.form.select.type.issue',
                'section' => 'paywall.step1.form.select.type.section',
                'article' => 'paywall.step1.form.select.type.article',
            ),
        ))
        ->add('isTemplate', 'checkbox', array(
            'label' => 'paywall.step1.form.label.istemplate',
            'required' => false,
        ))
        ->add('price', null, array(
            'label' => 'paywall.step1.form.label.price',
            'error_bubbling' => true,
            'required' => true,
            'precision' => 2,
            'invalid_message' => 'paywall.step1.form.error.price',
            'constraints' => array(
                new Assert\Range(array(
                    'min' => '0.01',
                    'max' => '99999999.99',
                    'minMessage' => 'paywall.errors.price.min',
                    'maxMessage' => 'paywall.errors.price.max',
                )),
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Newscoop\PaywallBundle\Entity\Subscription',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'subscriptionconf';
    }
}
