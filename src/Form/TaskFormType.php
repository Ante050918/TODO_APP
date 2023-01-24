<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('priority', ChoiceType::class, [
                'choices' => [
                    'Low' => 'Low',
                    'Normal' => 'Medium',
                    'High'=> 'High',
                ]
            ])
            ->add('deadline', ChoiceType::class, [
                'choices' => [
                    'in one hour' => new \DateTimeImmutable('now + 1 hour', new \DateTimeZone('Europe/Berlin')),
                    'tomorrow' => new \DateTimeImmutable('+1 day', new \DateTimeZone('Europe/Berlin')),
                    '1 week' => new \DateTimeImmutable('+1 week', new \DateTimeZone('Europe/Berlin')),
                    '1 month' => new \DateTimeImmutable('+1 month', new \DateTimeZone('Europe/Berlin')),
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}