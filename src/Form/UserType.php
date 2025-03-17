<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    // Construction du formulaire pour l'entité User
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le nom de l'utilisateur
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            // Champ pour la description de l'utilisateur
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            // Champ pour l'email de l'utilisateur
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            // Champ pour le mot de passe de l'utilisateur
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
            ]);
    }

    // Configuration des options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,  // Associe le formulaire à l'entité User
        ]);
    }
}
