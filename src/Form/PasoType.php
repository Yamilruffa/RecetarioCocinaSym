<?php

namespace App\Form;

use App\Entity\Paso;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\Receta;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class PasoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'attr' => [
                    'rows' => 7,  // Controla la altura inicial
                    'style' => 'width: 100%; resize: vertical;', // Ancho completo y permite ajustar altura
                    'placeholder' => 'Escribe aquí la descripción de la receta...', // Texto de ayuda
                ],
            ])
            ->add('numero', IntegerType::class, [
                'label' => false, // Oculta la etiqueta
                'attr' => [
                    'readonly' => true, // Evita que el usuario lo edite
                    'hidden' => true,   // Lo oculta visualmente sin afectar su envío
                    'min' => 1,
                    'step' => 1,
                ],
            ])
            
            ->add('receta', EntityType::class, [
                'class' => Receta::class,  // La clase de la entidad Receta
                'choice_label' => 'nombre', // El campo que se mostrará al usuario
                'data' => $options['receta_actual'] ?? null, // Preselecciona la receta si existe
                'attr' => ['style' => 'display: none;'], // Usando display: none para ocultar el campo
                'label' => false, // Esto oculta la etiqueta del campo
                'required' => true,  // Marca como obligatorio si es necesario
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paso::class,
            'receta_actual' => null,
        ]);
    }
}
