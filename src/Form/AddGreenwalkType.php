<?php

namespace App\Form;

use App\Entity\Greenwalk;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddGreenwalkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('timedate', DateTimeType::class, [
                'format' => 'Y-m-d H:i:s',
                'widget' => 'single_text',
            ])
            ->add('longitude')
            ->add('latitude')
            ->add('city')
            ->add('zipcode')
            ->add('description')
            ->add('street')
            ->add('author')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Greenwalk::class,
        ]);
    }
}
