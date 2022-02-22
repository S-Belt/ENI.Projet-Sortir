<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' =>'single_text'
            ])
            ->add('duree')
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionMax')
            ->add('infosSortie', TextareaType::class)
            ->add('campus', EntityType::class,['class'=>Campus::class,'choice_label'=>'nom',
                'disabled' => 'disabled'])
            ->add('ville', EntityType::class, ['class' => Ville::class, 'choice_label' => 'nom',
                'mapped' => false])
            ->add('Lieu', EntityType::class, ['class' =>Lieu::class, 'choice_label' => 'nom',
                'mapped' => false])
            ->add('rue', EntityType::class, ['class' => Lieu::class, 'choice_label' => 'rue',
                'mapped' => false])
            ->add('codePostal', EntityType::class, ['class' => Ville::class, 'choice_label' => 'codePostal',
                'mapped' => false] )
            //->add('etat', EntityType::class, ['class' =>Etat::class, 'choice_label' => 'libelle'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
