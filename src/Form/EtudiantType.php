<?php

namespace App\Form;

use App\Entity\Etudiant;
use App\Entity\Groupes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('N_Inscriptionn')
            ->add('CIN')
            ->add('Nom_Ar')
            ->add('Prenom_Ar')
            ->add('Nom_Fr')
            ->add('Prenom_Fr')
            ->add('Sexe')
            ->add('Situation_Familiale')
            ->add('Date_de_naissance' ,DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('Lieu_de_naissance_AR')
            ->add('Lieu_de_naissance_FR')
            ->add('Statut')
            ->add('Passeport')
            ->add('Adresse_Fr')
            ->add('Adresse_Ar')
            ->add('Code_gouvernorat')
            ->add('Email')
            ->add('Telephone_Fixe')
            ->add('Telephone_Portable')
            ->add('Code_Nature_Bac')
            ->add('Inscription')
            ->add('Groupe', EntityType::class , [
                    'class' => Groupes::class,
                'choice_label' => 'Nom'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
