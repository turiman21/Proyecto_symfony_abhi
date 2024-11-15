<?php

namespace App\Form;

use App\Entity\Alquiler;
use App\Entity\Producto;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Importa el tipo TextType
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class AlquilerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('precio')
            ->add('fecha_alquiler', null, [
                'widget' => 'single_text',
            ])
            ->add('fecha_devolucion', null, [
                'widget' => 'single_text',
            ])
            ->add('id_producto', EntityType::class, [
                'class' => Producto::class,
                'choice_label' => 'id',
            ])
            ->add('id_user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alquiler::class,
        ]);
    }
}
