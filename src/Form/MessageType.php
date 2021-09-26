<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('smsMessage', TextAreaType::class, [
                'label' => 'Сообщение для отправки по смс',
                'required' => false,
            ])
            ->add('emailMessage', TextAreaType::class, [
                'label' => 'Сообщение для отправки по email',
                'required' => false,
            ])
            ->add('save', SubmitType::class, array(
                'label' => 'Отправить'
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}