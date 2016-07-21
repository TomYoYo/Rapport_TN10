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


class FiltresType extends AbstractType
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
        $builder
            ->add('menu','choice',array(
                'required' => false,
                'choices' => $this->options['menu']
            ))->add('donnees','choice',array(
                'required' => false,
                'choices' => $this->options['donnees']
            ))->add('metier','choice',array(
                'required' => false,
                'choices' => $this->options['metier']
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
        return 'backoffice_habilitation_fitres';
    }
}