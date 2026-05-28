<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr'  => ['placeholder' => 'Ex : Faire la migration Doctrine'],
            ])
            ->add('priority', ChoiceType::class, [
                'label'   => 'Priorité',
                'choices' => [
                    'Pianissimo (pp)'  => 'pp',
                    'Piano (p)'        => 'p',
                    'Mezzo-forte (mf)' => 'mf',
                    'Forte (f)'        => 'f',
                    'Fortissimo (ff)'  => 'ff',
                ],
            ])
            ->add('movement', ChoiceType::class, [
                'label'   => 'Rythme',
                'choices' => [
                    'Allegro'  => 'allegro',
                    'Andante'  => 'andante',
                    'Adagio'   => 'adagio',
                    'Presto'   => 'presto',
                ],
            ])
            ->add('tags', TextType::class, [
                'label'    => 'Tags',
                'mapped'   => false,
                'required' => false,
                'attr'     => ['placeholder' => 'symfony, php, urgent'],
            ])
            ->add('dueDate', TextType::class, [
                'label'    => 'Échéance',
                'required' => false,
                'attr'     => ['placeholder' => '2025-12-31'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Ajouter la tâche',
                'attr'  => ['class' => 'btn-add'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}