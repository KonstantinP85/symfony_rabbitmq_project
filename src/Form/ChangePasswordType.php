<?php

declare(strict_types=1);

namespace App\Form;

use App\DtoModel\ChangePasswordDtoModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Старый пароль'
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Новый пароль'
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Подтверждение пароля'
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
            'data_class' => ChangePasswordDtoModel::class,
        ]);
    }
}