<?php

namespace App\Form;

use App\Entity\Producto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreProducto', TextType::class, [
                'label' => 'Nombre del Producto',
            ])
            ->add('precio_venta', NumberType::class, [
                'label' => 'Precio de Venta',
            ])
            ->add('precio_alquiler', NumberType::class, [
                'label' => 'Precio de Alquiler',
                'attr' => [
                    'id' => 'precioAlquiler',
                ],
            ])
            ->add('estado', TextType::class, [
                'label' => 'Estado del Producto',
            ])
            ->add('descripcion', TextType::class, [
                'label' => 'Descripcion',
            ])
            ->add('tipo', ChoiceType::class, [
                'label' => 'Tipo de Producto',
                'choices' => [
                    'Consola' => 'consola',
                    'Smartphone' => 'smartphone',
                    'Videojuego' => 'videojuego',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('imagen', FileType::class, [
                'label' => 'Imagen del producto',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*'],
            ])

            ->add('marca', ChoiceType::class, [
                'label' => 'Marca',
                'choices' => [
                    'Sony' => 'Sony',
                    'Microsoft' => 'Microsoft',
                    'Nintendo' => 'Nintendo',
                    'Samsung' => 'Samsung',
                    'Apple' => 'Apple',
                ],
                'placeholder' => 'Seleccione una marca',
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Stock',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Guardar Producto',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }
}
