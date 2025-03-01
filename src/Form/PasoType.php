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

class PasoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('numero')
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
