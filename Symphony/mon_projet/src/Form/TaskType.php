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

            // Champ titre → <input type="text"> lié à Task::$title.
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr'  => ['placeholder' => 'Ex : Faire la migration Doctrine'],
            ])

            // Champ priorité → valeurs compatibles avec le projet PHP/JS existant.
            // Le projet PHP utilise : high, medium, low (pas les valeurs musicales).
            // On garde les deux systèmes pour la compatibilité.
            ->add('priority', ChoiceType::class, [
                'label'   => 'Priorité',
                'choices' => [
                    'High'   => 'high',
                    'Medium' => 'medium',
                    'Low'    => 'low',
                ],
            ])

            // Champ mouvement = statut de la tâche.
            // IMPORTANT : on inclut 'moderato' pour la compatibilité avec les
            // tâches existantes créées par le projet PHP/JS (qui utilise
            // todo→andante, doing→moderato, done→allegro).
            // On ajoute aussi adagio et presto pour les nouvelles tâches Symfony.
            ->add('movement', ChoiceType::class, [
                'label'   => 'Statut',
                'choices' => [
                    'Andante  — À faire'    => 'andante',
                    'Moderato — En cours'   => 'moderato',  // ← compatibilité PHP/JS
                    'Allegro  — Terminé'    => 'allegro',
                    'Adagio   — En attente' => 'adagio',
                    'Presto   — Urgent'     => 'presto',
                ],
            ])

            // Champ tags → 'mapped: false' car on reçoit une string CSV
            // ("php, urgent") qu'on convertit en tableau dans le contrôleur.
            // La BDD stocke un tableau JSON, pas une string.
            ->add('tags', TextType::class, [
                'label'    => 'Tags (séparés par des virgules)',
                'mapped'   => false,
                'required' => false,
                'attr'     => ['placeholder' => 'symfony, php, urgent'],
            ])

            // Champ date d'échéance → format YYYY-MM-DD, optionnel.
            ->add('dueDate', TextType::class, [
                'label'    => 'Échéance',
                'required' => false,
                'attr'     => ['placeholder' => '2026-12-31'],
            ])

            // Bouton submit.
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr'  => ['class' => 'btn-submit'],
            ])
        ;
    }

    // Lie ce formulaire à l'entité Task.
    // Symfony sait automatiquement quels setters appeler pour chaque champ.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}