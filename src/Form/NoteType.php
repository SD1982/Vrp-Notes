<?php

namespace App\Form;

use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
            ))
            ->add('montant')
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'choices' => array(
                        '' => null,
                        'Carburant' => 'Carburant',
                        'Transport' => 'Transport',
                        'Restauration' => 'Restauration',
                        'Hébergement' => 'Hébergement',
                        'Autres' => 'Autres',
                    ),
                )
            )
            ->add('scan', FileType::class, array('label' => 'Scan (Fichier PDF)'))
            ->add('description', TextareaType::class)
            ->add('adress')
            ->add('postcode')
            ->add('city')
            ->add('country')
            ->add('latitude')
            ->add('longitude');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
