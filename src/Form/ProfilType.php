<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('telephone', TextType::class, [
                'constraints'=> [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le numero de telephone doit faire 10 chifrres!',
                        'max' => 10,
                        'maxMessage' => 'Le numero de telephone doit faire 10 chifrres!'
                    ])
                ]
            ])
            ->add('email')
            ->add('password', PasswordType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre!',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirmation']
            ])
            ->add('campus', EntityType::class,['class'=>Campus::class,'choice_label'=>'nom'])
            ->add('photo', FileType::class, [
                'label' => 'photo de profil',
                'mapped' => false,
                'required' =>false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/gif',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'On n\'accepte que des .jpg, .png, ou .gif'
                    ])

                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
