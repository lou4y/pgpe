<?php

namespace App\Form;

use App\Entity\Groupes;
use App\Entity\Matiere;
use App\Entity\Professeur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Time;

class MatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom')
            // a choice box with the days of the week
            ->add('Jour',ChoiceType::class,[
                'choices' => [
                    'Lundi' => 'Lundi',
                    'Mardi' => 'Mardi',
                    'Mercredi' => 'Mercredi',
                    'Jeudi' => 'Jeudi',
                    'Vendredi' => 'Vendredi',
                    'Samedi' => 'Samedi',
                    'Dimanche' => 'Dimanche',
                ],])
            ->add('debut',TimeType::class,[
                'widget'=>'single_text',
                 ])

            ->add('fin',TimeType::class)
            ->add('professeur',EntityType::class,[
                'class' => Professeur::class,
                'choice_label' => 'Nom' ,

            ])
            ->add('groupe',EntityType::class,[
                'class' => Groupes::class,
                'choice_label' => 'Nom' ,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matiere::class,
        ]);
    }
}
