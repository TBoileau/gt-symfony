<?php

declare(strict_types=1);

namespace App\Form;

use App\DataTransformer\TagsTransformer;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PostType extends AbstractType
{
    public function __construct(private TagsTransformer $tagsTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'empty_data' => ''
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'empty_data' => ''
            ])
            ->add('tags', TextType::class, [
                'label' => 'Tags',
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'required' => in_array('create', $options['validation_groups'] ?? [])
            ]);

        $builder->get('tags')->addModelTransformer($this->tagsTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Post::class);
    }
}
