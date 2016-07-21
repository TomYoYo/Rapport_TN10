<?php

namespace Transverse\PartenaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ParametrageType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titreParam', 'text', array(
                'label' => 'Nom du paramètre :',
            ))
            ->add('prefixeSpool', 'text', array(
                'label' => 'Préfixe du Spool :'
            ))
            ->add('priorite', 'choice', array(
                'choices'   => array('0' => 'Faible', '1' => 'Normale', '2' => 'Haute'),
                'required'  => true,

                'label' => 'Priorité :',
            ))
            ->add('commentaire', 'textarea', array(
                'label' => 'Commentaire :',
                'required'  => false,
            ))
            ->add('tags', 'collection', array(
                'type'         => new TagType(),
                'label' => false,
                'required'  => false,
                'allow_add'    => true,
                'allow_delete' => true,
                'delete_empty' => true,

            ))
            ->add('isFiltreIncluded', 'choice', array(
                'choices'   => array('0' => 'On récupère le spool', '1' => 'On ne récupère pas le spool'),
                'required'  => true,
                'label' => 'Si le contenu du filtre est présent dans un spool :',
            ))
            ->add('filtres', 'collection', array(
                'type'         => new FiltreType(),
                'label' => false,
                'required'  => false,
                'allow_add'    => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => 'false',
            ))
            ->add('roles', 'entity', array(
                'class'    => 'TransversePartenaireBundle:Role',
                'property' => 'nomRole',
                'attr'      => array('inline' => false),
                'multiple' => true,
                'required'  => false,
                'expanded' => true,
            ))
            ->add('valider', 'submit', array(
                'label' => 'Valider',))
            ;
    }
   

    
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Transverse\PartenaireBundle\Entity\Parametrage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'transverse_partenairebundle_parametrage';
    }
}
