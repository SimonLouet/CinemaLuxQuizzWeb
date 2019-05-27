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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PartieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array('label' => 'Nom'))
            ->add('modejeux', ChoiceType::class, ['label' => 'Mode de jeux',
                                                  'choices'  => [
                                                      'Tour par tour' => "TourParTour",
                                                      'Makey Makey' => "MakeyMakey",
                                                  ],
                                              ])
            ->add('date', DateTimeType::class, array('input' => 'datetime',
                                                          'widget' => 'single_text',
                                                          'format' => 'dd/MM/yyyy',
                                                          'required' => true,
                                                          'label' =>'Date de la partie',
                                                          'placeholder' => 'jj/mm/aaaa'))
            ->add('theme', TextType::class, array('label' => 'thème'))
            ->add('imagefondname', FileType::class, array('label' => false,'attr' => [ 'placeholder' => 'Choose file'],'required' => false))
            ->add('fontpolice', TextType::class, array('label' => 'Nom police google (https://fonts.google.com/)',  'data' => 'Roboto'))
            ->add('fontsize', NumberType::class, array('label' => 'Taille police',  'data' => '40.0'))
            ->add('colortitre', ColorType::class, array('label' => 'Couleur des titres'))
			->add('genre_id', ChoiceType::class, [
					'choices'  => [
						'Maybe' => null,
						'Yes' => true,
						'No' => false,
					],
				])
            ->add('colortext', ColorType::class, array('label' => 'Couleur des textes'))
            ->add('colorfenetre', ColorType::class, array('label' => 'Couleur des fenètre'))
			      ->add('ajouter', SubmitType::class, array('label' => 'Nouvelle partie'))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

            'mapped' => false,
            'data_class' => Partie::class,
        ]);
    }
}
