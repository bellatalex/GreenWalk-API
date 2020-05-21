<?php

namespace App\Form;

use App\Entity\Greenwalk;
use Doctrine\DBAL\Types\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddGreenwalkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('timedate')
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
