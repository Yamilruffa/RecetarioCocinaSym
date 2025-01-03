<?php

namespace App\Form;

use App\Entity\Receta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//
use App\Entity\Categoria;
use App\Entity\Ingrediente;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RecetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('png')
            ->add('descripcion')
            ->add('tiempoprep')
            ->add('porciones')
            ->add('dificultad')
            // multiple choice para categoria e ingrediente
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'multiple' => true, // Permite seleccionar más de una categoría
                'expanded' => true, // Mostrar como casillas de verificación (checkbox)
                'choice_label' => 'nombre'
            ])
            ->add('ingredientes', EntityType::class, [
                'class' => Ingrediente::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'nombre'
            ])
            //escondo el usuario
            ->add('usuario', HiddenType::class,[
                'label'=> false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Receta::class,
        ]);
    }
}
