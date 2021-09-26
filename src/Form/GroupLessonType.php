<?php

declare(strict_types=1);

namespace App\Form;

use App\DtoModel\GroupLessonDtoModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupLessonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Название'
            ])
            ->add('firstNameTrainer', TextType::class, [
                'label' => 'Имя тренера'
            ])
            ->add('lastNameTrainer', TextType::class, [
                'label' => 'Фамилия тренера'
            ])
            ->add('patronymicTrainer', TextType::class, [
                'label' => 'Отчество тренера',
                'required' => false,
            ])
            ->add('description', TextAreaType::class, [
                'label' => 'Описание'
            ])
            ->add('save', SubmitType::class, array(
                'label' => 'Сохранить'
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GroupLessonDtoModel::class,
        ]);
    }
}
