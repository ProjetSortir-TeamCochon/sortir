<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom de la sortie'
            ])
            ->add('dateHeureDebut' , DateTimeType::class,[
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'label' => 'Date & heure de début'
            ])
            ->add('duree', IntegerType::class,[
                'label' => 'Durée (en minutes)'
            ])
            ->add('dateLimiteInscription' , DateType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription'
            ])
            ->add('nbInscriptionsMax', IntegerType::class,[
                'label' => 'Nombre d\'inscription maximum'
            ])
            ->add('infosSortie', TextareaType::class,[
                'label' => 'Description'
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Nom du campus'
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'label' => 'Lieu de la sortie',
                'choice_label' => function ($lieu) {return $lieu->getNom();}
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
