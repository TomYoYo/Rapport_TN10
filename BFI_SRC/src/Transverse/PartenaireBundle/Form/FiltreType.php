<?php

namespace Transverse\PartenaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FiltreType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('expressionAFiltrer', 'text', array(
                'required'  => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Transverse\PartenaireBundle\Entity\Filtre'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'transverse_partenairebundle_filtre';
    }
}
