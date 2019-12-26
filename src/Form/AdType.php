<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'title'])
            ->add('content', TextareaType::class,
                [   'label' => 'Contenu de l\'annonce',
                    'attr' => ['rows' => '15']
                ])
            ->add('createdAt', DateType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text'
            ])
            ->add('photo', FileType::class, [
                'label' => 'Télécharger la photo',
                'required' => false
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('submit', SubmitType::class,
                ["label" => "Valider", "attr" => ["class" => "btn btn-success"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
