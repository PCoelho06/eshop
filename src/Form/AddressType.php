<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => 'Donnez un nom à cette adresse',
            ])
            ->add('firstname', TextType::class, [
                "label" => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                "label" => 'Nom',
            ])
            ->add('company', TextType::class, [
                "label" => 'Société',
                "required" => false,
            ])
            ->add('address', TextType::class, [
                "label" => 'Adresse',
            ])
            ->add('postal', TextType::class, [
                "label" => 'Code postal',
            ])
            ->add('city', TextType::class, [
                "label" => 'Ville',
            ])
            ->add('country', TextType::class, [
                "label" => 'Pays',
            ])
            ->add('phone', TextType::class, [
                "label" => 'Téléphone',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => "btn btn-success col-12"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
