<?php

namespace App\Form;

use App\Entity\ReponsePossible;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ReponsePossibleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class, array('label' => 'Intitulé de la réponse :'))
            ->add('piecejointe', TextType::class, array('label' => 'Pièce jointe :','required' => false))
            ->add('correct', CheckboxType::class,array('required' => false))
			
			->add('ajouter', SubmitType::class, array('label' => 'Ajouter la réponse'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReponsePossible::class,
        ]);
    }
}
