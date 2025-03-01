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
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityRepository;



class RecetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('png', FileType::class, [
                'label' => 'Imagen',
                'mapped' => false,  // No está mapeado directamente a la propiedad 'png' de la entidad
                'required' => false, // No es obligatorio
                'attr' => ['accept' => 'image/*'], // Acepta todo tipo de imágenes
            ])
            ->add('descripcion')
            ->add('tiempoprep', null, [
                'label' => 'Tiempo de preparación'
            ])
            ->add('porciones')
            ->add('dificultad', ChoiceType::class, [
                'choices' => [
                    'Fácil' => 'Fácil',
                    'Intermedio' => 'Intermedio',
                    'Difícil' => 'Difícil',
                ], // Opciones de selección
            ])
            // multiple choice para categoria e ingrediente
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'multiple' => true, // Permite seleccionar más de una categoría
                'expanded' => true, // Mostrar como casillas de verificación (checkbox)
                'choice_label' => 'nombre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nombre', 'ASC'); // Ordena las categorías por nombre ascendente
                },
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
                'mapped'=> false // esto hace que el formulario no sobreescriba el valor de usuario que le pasamos de la página anterior.
            ]);

        // Mostrar el campo 'visible' solo si el usuario es admin
        if (isset($options['user']) && in_array('ROLE_ADMIN', $options['user']->getRoles())) {
            $builder->add('visible', ChoiceType::class, [
                'choices' => [
                    'Sí' => '1',
                    'No' => '0',
                ],
                'data' => '0', // Valor por defecto
            ]);
        } else {
            $builder->add('visible', HiddenType::class, [
                'mapped' => false,
                'data' => '0', // Valor por defecto 'No'
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Receta::class,
            'user' => null, // Se agrega esta opción para recibir el usuario
        ]);
    }
}
