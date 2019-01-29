<?php

;namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class, array('label' => 'Intitulé de la question'))
            ->add('timer', IntegerType::class, array('label' => 'Temp pour répondre (milliseconds)'))
            ->add('videoyoutube', TextType::class, array('label' => 'Code video youtube (ex : ByzB0rWuLTQ)','required' => false))
            ->add('piecejointe', FileType::class, array('label' => 'Pièce jointe','required' => false))
            ->add('fontsize', NumberType::class, array('label' => 'Taille police',  'data' => '50.0'))
      			->add('ajouter', SubmitType::class, array('label' => 'Ajouter la question'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
