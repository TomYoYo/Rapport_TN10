<?php

namespace BackOffice\CustomerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idCustomer')
            ->add('designation')
            ->add('dateNaissance','date',array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false
            ))
            ->add('siren')
            ->add('codeNic')
            ->add('codePays')
            ->add('capital')
            ->add('Juridique','entity',array(
                'class' => 'BackOfficeCustomerBundle:SettingsJuridique',
                'property' => 'intitule',
                'empty_value' => '',
                'required' => false
            ))
            ->add('categorieClient','entity',array(
                'class' => 'BackOfficeCustomerBundle:SettingsCategorie',
                'property' => 'code',
                'empty_value' => '',
                'required' => false
            ))
            ->add('chiffreAffaire')
            ->add('qualiteClient','entity',array(
                'class' => 'BackOfficeCustomerBundle:SettingsQuality',
                'property' => 'intitule',
                'empty_value' => '',
                'required' => false
            ))
            ->add('adresse')
            ->add('tel')
            ->add('fax')
            ->add('codeApe')
            ->add('coteActivite')
            ->add('cotecredit')
            ->add('email')
            ->add('cP')
            ->add('ville')
            ->add('pays')
            ->add('dateCreation','date',array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'empty_value' => '',
                'required' => false
            ))
            ->add('statut')
            ->add('codeCivilite','entity',array(
                'class' => 'BackOfficeCustomerBundle:SettingsCivility',
                'property' => 'intitule',
                'empty_value' => '',
                'required' => false
            ))
            ->add('codeEtat','entity',array(
                'class' => 'BackOfficeCustomerBundle:SettingsStateCode',
                'property' => 'intitule',
                'empty_value' => '',
                'required' => false
            ))
            ->add('responsable')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BackOffice\CustomerBundle\Entity\Customer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'backoffice_customerbundle_customer';
    }
}
