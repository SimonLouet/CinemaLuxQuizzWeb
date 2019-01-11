<?php

namespace App\Form;

use App\Entity\Partie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

class PartieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array('label' => 'Nom'))
            ->add('theme', TextType::class, array('label' => 'thème'))
            ->add('description', TextareaType::class, array('label' => 'Présentation'))
            ->add('imagefondname', FileType::class, array('label' => 'Image de fond',
                                                          'required' => false))
            ->add('colortitre', ColorType::class, array('label' => 'Couleur des titres'))
            ->add('colortext', ColorType::class, array('label' => 'Couleur des textes'))
            ->add('colorchrono', ColorType::class, array('label' => 'Couleur des chronomètre'))
            ->add('date', DateTimeType::class, array('input' => 'datetime',
                                                          'widget' => 'single_text',
                                                          'format' => 'dd/MM/yyyy',
                                                          'required' => true,
                                                          'label' =>'Date de la partie',
                                                          'placeholder' => 'jj/mm/aaaa'))

			->add('ajouter', SubmitType::class, array('label' => 'Nouvelle partie'))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Partie::class,
        ]);
    }
}
