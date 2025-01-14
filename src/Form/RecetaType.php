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
use Symfony\Component\Form\Extension\Core\Type\FileType;


class RecetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('png', FileType::class, [
                'label' => 'Imagen PNG',
                'mapped' => false,  // No está mapeado directamente a la propiedad 'png' de la entidad
                'required' => false, // No es obligatorio
                'attr' => ['accept' => 'image/png'], // Solo permite imágenes PNG
            ])
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
                'label'=> false, // esto hace que no se muestre en el formulario, para que el usuario no lo pueda modificar.
                'mapped'=> false // esto hace que el formulario no sobreescriva el valor de usuario que le pasamos de la pagina anterior.
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
