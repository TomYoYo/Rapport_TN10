<?php

namespace BackOffice\EvolutionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EvolutionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('versionNumber')
            ->add('preprodRealisation', 'date', array(
                'widget' => 'single_text',
                'format'=> 'dd/MM/yyyy',
                'required' => false
            ))
            ->add('prodRealisation', 'date', array(
                'widget' => 'single_text',
                'format'=> 'dd/MM/yyyy',
                'required' => false
            ))
            ->add('estimatedDate', 'date', array(
                'widget' => 'single_text',
                'format'=> 'dd/MM/yyyy',
                'required' => false
            ))
            ->add('description')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BackOffice\EvolutionBundle\Entity\Evolution'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'backoffice_evolutionbundle_evolution';
    }
}
