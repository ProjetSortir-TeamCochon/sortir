<?php

namespace App\Form;

use App\Entity\Campus;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class SearchFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Filtrer par campus...',
                'label' => 'Campus',
                'required'=> false
            ])
            ->add('minDate', DateType::class, [
                'label' => 'Entre',
                'html5' => true,
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('maxDate', DateType::class, [
                'label' => 'et',
                'html5' => true,
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('filters', ChoiceType::class, [
                'label' => 'Filtrer par',
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    'manager' => 'manager',
                    'registered' => 'registered',
                    'notRegistered' => 'notRegistered',
                    'past' => 'past',
                ],
                'choice_label' => function ($choice, $key, $value){
                    switch ($key){
                        case 'manager':
                            return 'Sorties dont je suis l\'organisateur/trice';
                        case 'registered':
                            return 'Sorties auxquelles je suis inscrit/e';
                        case 'notRegistered':
                            return 'Sorties auxquelles je ne suis pas inscrit/e';
                        case 'past':
                            return 'Sorties passées';
                    }
                },
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
