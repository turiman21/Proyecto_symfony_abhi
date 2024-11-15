<?php

namespace App\Form;

use App\Entity\Reparacion;
use App\Entity\Producto;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReparacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idProducto', EntityType::class, [
                'class' => Producto::class,
                'choice_label' => 'nombre_producto',
                'label' => 'Producto',
                'placeholder' => 'Selecciona un producto',
            ])
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nombre',
                'label' => 'Cliente',
                'placeholder' => 'Selecciona un cliente',
            ])
            ->add('estado_reparacion', ChoiceType::class, [
                'choices' => [
                    'Pendiente' => 'pendiente',
                    'En Proceso' => 'en_proceso',
                    'Completada' => 'completada',
                ],
                'label' => 'Estado de la Reparación',
            ])
            ->add('costo_reparacion', NumberType::class, [
                'label' => 'Costo de Reparación (€)',
                'scale' => 2,
            ])
            ->add('fecha_entrada', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha de Entrada',
                'data' => new \DateTime(), // Valor predeterminado de la fecha actual
            ])
            ->add('fecha_salida', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha Estimada de Salida',
                'required' => false,
                'data' => new \DateTime(), // Valor predeterminado de la fecha actual
            ])
            ->add('descripcionUsuario', TextareaType::class, [
                'label' => 'Descripción del Usuario',
                'required' => false,
                'attr' => [
                    'readonly' => true,
                    'placeholder' => 'Descripción proporcionada por el usuario...',
                ],
            ])
            ->add('notaTecnico', TextareaType::class, [
                'label' => 'Nota del Técnico',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Escribe una nota interna...',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reparacion::class,
        ]);
    }
}
