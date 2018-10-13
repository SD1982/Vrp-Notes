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
            ->add('scan', fileType::class, array(
                'label' => 'Scan de la note',
                'required' => false,
                'data_class' => null
            ))
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'datepicker'
                ],
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
            ->add('description', TextareaType::class, array(
                'required' => false
            ))
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
