<?php
/**
 * Created by PhpStorm.
 * User: t.pueyo
 * Date: 10/03/2016
 * Time: 10:18
 */

namespace BackOffice\HabilitationBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ServiceType extends AbstractType
{

    public function __construct($options = null)
    {
        $this->options = $options;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if($this->options)
        {
            $builder
                ->add('services','choice',array(
                    'required' => false,
                    'choices' => array(
                        0 => $this->options['service'],
                    ),
                    'empty_value' => false
                ))->add('sous_services','choice',array(
                    'required' => false,
                    'choices' => array(
                        0 => $this->options['sous_service'],
                    ),
                    'empty_value' => false

                ));
        }
        else
        {
            $builder
                ->add('services','choice',array(
                    'required' => false,
                ))->add('sous_services','choice',array(
                    'required' => false
                ));
        }

    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'validation_groups' => false
        ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'backoffice_habilitation_';
    }
}