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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class RecetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('png', FileType::class, [
                'label' => 'Imagen',
                'mapped' => false,  // No está mapeado directamente a la propiedad 'png' de la entidad
                'required' => true,
                'attr' => ['accept' => 'image/*'], // Acepta todo tipo de imágenes
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'attr' => [
                    'rows' => 7,  // Controla la altura inicial
                    'style' => 'width: 100%; resize: vertical;', // Ancho completo y permite ajustar altura
                    'placeholder' => 'Escribe aquí la descripción de la receta...', // Texto de ayuda
                ],
            ])
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
                'choice_label' => 'nombre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nombre', 'ASC');
                },
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
                    'Sí' => 'si',
                    'No' => 'no',
                ],
                'data' => 'no', // Valor por defecto "no" para admin
            ]);
        } else {
            // Si el usuario no es administrador, establecer 'visible' como 'no' y ocultar el campo
            $builder->add('visible', HiddenType::class, [
                'data' => 'no', // Valor por defecto 'no' para usuarios no admin
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
