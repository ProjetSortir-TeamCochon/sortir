<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Range;

class SearchFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Filtrer par campus...',
                'label' => 'Par Campus',
                'required'=> false
            ])
            ->add('minDate', DateType::class, [
                'label' => 'Sorties entre',
                'html5' => true,
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Range([
                        'min' => (new \DateTime())->sub(new \DateInterval('P1M')),
                        'max' => '+1 year',
                        'minMessage' => "Impossible de rechercher les sorties datant de plus d'un mois.",
                        'maxMessage' => "Impossible de rechercher les sorties datant de plus d'un mois.",
                    ])
                ]
            ])
            ->add('maxDate', DateType::class, [
                'label' => 'et',
                'html5' => true,
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Range([
                        'min' => (new \DateTime())->sub(new \DateInterval('P1M')),
                        'max' => '+1 year',
                        'minMessage' => "Impossible de rechercher les sorties datant de plus d'un mois.",
                        'maxMessage' => "Impossible de rechercher les sorties datant de plus d'un mois.",
                    ])
                ]
            ])
            ->add('filters', ChoiceType::class, [
                'label' => 'Affiner ma recherche par',
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
