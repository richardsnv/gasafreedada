<?php

namespace App\Form;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('quantity', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    
                    'label' => 'Quantité (Go)',
                ]])
            ->add('price', MoneyType::class, [
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label' => 'Prix (FCFA)',
                'currency' => 'XOF',
                
                
            ])
            ->add('deadLine', IntegerType::class, [
                'label' => 'Durée (en jours)',
                'attr' => [
                   'class'=> 'form-control'
                ],
            ])
            ->add('type', ChoiceType::class, [
                
                    'label' => 'Type d\'offre',
                    'choices' => [
                        'Illimité' => 'ILLIMITE',
                        'Limité' => 'LIMITE',
                    ],
                    'expanded' => true, 
                    'multiple' => false, 
                    'attr' => [
                        'class' => 'form-check', 
                    ],
                ])
            // ->add('createdAt', DateTimeType::class, [
            //     'label' => 'Date de création',
            //     'widget' => 'single_text',
            //     'attr' => [
            //     ],
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}
