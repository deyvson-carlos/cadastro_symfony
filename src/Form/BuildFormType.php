<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\CadastroPessoas;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Type;

class BuildFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nome', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('tipoPessoa', ChoiceType::class, [
                'choices' => [
                    'Pessoa Física' => 'Fisica',
                    'Pessoa Jurídica' => 'Juridica',
                ],
                'label' => 'Tipo',
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('documento', IntegerType::class, [
                'label' => 'Documento',
                'attr' => [
                    'class' => 'form-control',
                    'data-tipo-mask' => true,
                    'placeholder' => 'CPF ou CNPJ',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric', 'message' => 'O documento deve conter apenas números.']),
                ],
            ])

            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Length(['min' => 6]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/',
                        'message' => 'A senha deve ter pelo menos 6 caracteres, incluindo pelo menos 1 maiúsculo, 1 minúsculo, 1 número e 1 caractere especial.',
                    ]),
                ],
            ])
            ->add('dataNascimento', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CadastroPessoas::class,
        ]);
    }
}
