<?php

namespace Transverse\PartenaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ParametrageEditType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'transverse_partenairebundle_parametrage_edit';
    }
    
    public function getParent()
    {
        return new ParametrageType();
    }
}
