<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
                ])
            ->add('ville', EntityType::class, ['class' => Ville::class, 'choice_label' => 'nom',
                'mapped' => false])
            ->add('Lieu', EntityType::class, ['class' =>Lieu::class, 'choice_label' => 'nom',
                ])
            ->add('rue', EntityType::class, ['class' => Lieu::class, 'choice_label' => 'rue',
                'mapped' => false])
            ->add('codePostal', EntityType::class, ['class' => Ville::class, 'choice_label' => 'codePostal',
                'mapped' => false] )
            /*************************************************************/
            /*->add('ville', EntityType::class, [
                'class' => Ville::class,
                'placeholder' => '',
                'mapped' => false
            ])*/
        ;

        /*$formModifier = function (FormInterface $form, Lieu $lieu = null, Ville $ville = null) {
            $lieux = null === $ville? [] : $ville->getLieus();

            $form->add('Lieu', EntityType::class, [
                'class' => Lieu::class,
                'placeholder' => '',
                'choices' => $lieux,
            ]);
        };


            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($formModifier){

                    $data = $event->getData();
                   $formModifier($event->getForm(), $data->getLieu()->getVille());


                }
            );

            $builder->get('ville')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($formModifier){
                    $ville = $event->getForm()->getData();

                    $formModifier($event->getForm()->getParent(), $ville);
                }
            );*/

    }




    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
