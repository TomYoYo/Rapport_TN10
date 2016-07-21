<?php

namespace Fiscalite\ODBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OperationType extends AbstractType
{
    public function __construct($odManager)
    {
        $this->odManager = $odManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codeOpe', 'choice', array('max_length' => 3, 'choices' => $this->odManager->getCodesOpe()))
            ->add('codeEve', 'text', array('max_length' => 3))
            ->add('numPiece', 'text', array('read_only' => true, 'max_length' => 6))
            ->add('numPieceTech', 'text', array('read_only' => true, 'max_length' => 6))
            ->add('profil', 'entity', array('disabled'   => true,
                'class' => 'BackOfficeUserBundle:Profil'))
            ->add('tiers', 'text', array('required' => false, 'max_length' => 7))
            ->add('dateCpt', 'date', array('widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'max_length' => 10))
            ->add('dateSai', 'date', array('read_only' => true, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'max_length' => 10))
            ->add('devise', 'text', array('read_only' => true, 'max_length' => 3))
            ->add('dateVal', 'date', array('widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'max_length' => 10))
            ->add('refLet', 'text', array('required' => false, 'max_length' => 7))
            ->add('refAnalytique', 'choice', array('max_length' => 3,
                'choices' => array(''=>'', '000'=>'000', '001'=>'001', '002'=>'002')))
            ->add('isComplementaryDay', 'choice', array('required' => false, 'expanded' => false, 'multiple' => false,
                'choices' => array('0' => 'Non', '1' => 'Oui'), 'empty_value' => false))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fiscalite\ODBundle\Entity\Operation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'od_odbundle_operation';
    }
}
