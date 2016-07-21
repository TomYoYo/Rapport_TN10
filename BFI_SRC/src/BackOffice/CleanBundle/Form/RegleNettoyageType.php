<?php

namespace BackOffice\CleanBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegleNettoyageType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add(
                'originServer',
                'choice',
                array(
                    'choices'   => array('sab' => 'SAB core'),
                    'label' => 'Serveur'
                )
            )
            ->add(
                'originDir',
                'choice',
                array(
                    'choices'   => array(
                        'fichiers_externes' => 'fichiers_externes',
                        'PRT01' => 'PRT01',
                        'f_interface' => 'f_interface'
                    ),
                    'label' => 'Dossier'
                )
            )
            ->add(
                'action',
                'choice',
                array(
                    'choices'   => array('compresser'=>'Déplacer & Compresser'),
                    'label'     => ' '
                )
            )
            ->add('sousDossier', 'text', array('required'=>false))
            ->add(
                'age',
                'choice',
                array(
                    'choices'=> array(
                        '1mois'=>'1 mois et 1 jour',
                        '2mois'=>'2 mois et 1 jour',
                        '6mois'=>'6 mois et 1 jour',
                        '14mois'=>'14 mois et 1 jour'
                    ),
                    'label' => 'Age du fichier'
                )
            )
            ->add(
                'destinationServer',
                'choice',
                array(
                    'choices'   => array('sab' => 'SAB core', 'win' => 'Arbo banque', 'bfi' => 'Serveur bfi'),
                    'label'     => 'Serveur'
                )
            )
            /*
            ->add(
                'destinationDir',
                'choice',
                array(
                    'choices'   => array(
                        'fichiers_externes' => 'fichiers_externes',
                        'PRT01' => 'PRT01',
                        'f_interface' => 'f_interface'
                    ),
                    'required'  => false,
                    'label'     => 'Dossier'
            ))
            */
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BackOffice\CleanBundle\Entity\RegleNettoyage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'backoffice_cleanbundle_reglenettoyage';
    }
}
