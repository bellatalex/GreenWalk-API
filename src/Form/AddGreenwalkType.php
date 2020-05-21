<?php

namespace App\Form;

use App\Entity\Greenwalk;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddGreenwalkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('timedate', DateTimeType::class, [
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'widget' => 'single_text',
            ])
            ->add('longitude', NumberType::class)
            ->add('latitude', NumberType::class)
            ->add('city', TextType::class)
            ->add('zipcode', TextType::class)
            ->add('description', TextType::class)
            ->add('street', TextType::class)
            ->add('author');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Greenwalk::class,
        ]);
    }
}
