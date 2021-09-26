<?php

declare(strict_types=1);

namespace App\Form;

use App\DtoModel\CreateUserDtoModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    protected RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Имя'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия'
            ])
            ->add('patronymic', TextType::class, [
                'label' => 'Отчество',
                'required' => false,
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Дата рождения'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон'
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Пол',
                'choices'  => [
                    'Мужской' => 'male',
                    'Женский' => 'female'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Фото',
                'required' => false,
            ])
            ->add('save', SubmitType::class, array(
                'label' => 'Сохранить'
            ))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreateUserDtoModel::class,
        ]);
    }
}
