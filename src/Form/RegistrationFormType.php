<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
            ])
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
            ])
            ->add('apellido1', TextType::class, [
                'label' => 'Primer Apellido',
            ])
            ->add('apellido2', TextType::class, [
                'label' => 'Segundo Apellido',
            ])
            ->add('direcccion', TextType::class, [
                'label' => 'Dirección',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Contraseña',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingresa una contraseña',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rol',
                'mapped' => false,
                'choices' => [
                    'Usuario' => 'ROLE_USER',
                    'Administrador' => 'ROLE_ADMIN',
                    'Técnico Consolas' => 'ROLE_CONSOLE_TECH',
                    'Técnico Telefonía' => 'ROLE_TELEPHONY_TECH',
                ],
                'multiple' => true,
                'expanded' => true, // Para mostrar como checkbox
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
