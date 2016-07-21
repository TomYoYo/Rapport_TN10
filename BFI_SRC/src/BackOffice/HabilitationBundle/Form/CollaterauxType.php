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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CollaterauxType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Date','text',array(
                'required' => false,
            ))->add('user','text',array(
                'required' => true
            ));
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'backoffice_habilitation_date';
    }
}