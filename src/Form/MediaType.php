<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MediaType extends AbstractType
{
    // Construction du formulaire pour l'entité Media
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour télécharger une image
            ->add('file', FileType::class, [
                'label' => 'Image',
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG, PNG ou GIF.',
                        'maxSizeMessage' => 'Le fichier ne doit pas dépasser {{ limit }} {{ suffix }}.',
                    ])
                ]
            ])
            // Champ pour entrer le titre de l'image
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ]);

        // Si l'utilisateur est administrateur, ajouter les champs supplémentaires
        if ($options['is_admin']) {
            $builder
                ->add('user', EntityType::class, [
                    'label' => 'Utilisateur',
                    'required' => false,
                    'class' => User::class,
                    'choice_label' => 'name',
                ])
                ->add('album', EntityType::class, [
                    'label' => 'Album',
                    'required' => false,
                    'class' => Album::class,
                    'choice_label' => 'name',
                ]);
        }
    }

    // Configuration des options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,  // Associe le formulaire à l'entité Media
            'is_admin' => false,           // Option pour contrôler les champs visibles pour l'admin
        ]);
    }
}
