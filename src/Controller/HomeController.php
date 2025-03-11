<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//

use App\Entity\Categoria;
use App\Form\CategoriaType;
use App\Repository\CategoriaRepository;
use App\Entity\Receta;
use App\Form\RecetaType;
use App\Repository\RecetaRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(RecetaRepository $recetaRepository, CacheInterface $cache, CategoriaRepository $CategoriaRepository): Response
    {
        // Obtener todas las recetas visibles
        $recetas = $recetaRepository->createQueryBuilder('r')
            ->where('r.visible = :visible')
            ->setParameter('visible', 'si')
            ->getQuery()
            ->getResult();

        // Obtener todas las categorías
        $categorias = $CategoriaRepository->findAll();

        // Obtener una receta aleatoria y almacenarla en caché por 24 horas
        $recetaRandom = $cache->get('receta_random', function (ItemInterface $item) use ($recetaRepository) {
            $item->expiresAfter(86400); // 24 horas
        
            $entityManager = $recetaRepository->getEntityManager();
            $connection = $entityManager->getConnection();
        
            // Obtener un ID aleatorio
            $sql = "SELECT id FROM receta WHERE visible = 'si' ORDER BY RAND() LIMIT 1";
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery()->fetchOne();
        
            return $result ? $entityManager->getRepository(Receta::class)->find($result) : null;
        });

        return $this->render('home/index.html.twig', [
            'recetas' => $recetas,
            'recetaRandom' => $recetaRandom,
            'categorias' => $categorias, // Pasar las categorías a la vista
        ]);
    }


    // Ruta para mostrar los detalles de una receta
    #[Route('/receta/{id}', name: 'vista_receta_show')]
    public function show(Receta $receta): Response
    {
        if ($receta->getVisible() !== 'si') {
            throw $this->createNotFoundException('La receta no está disponible.');
        }
        
        return $this->render('vista_receta/show.html.twig', [
            'recetum' => $receta,
        ]);
    }

    #[Route('/vista/receta/lista/{categoria}', name: 'app_vista_receta_lista', methods: ['GET'])]
    public function lista(RecetaRepository $recetaRepository, CategoriaRepository $categoriaRepository, string $categoria): Response
    {
        $categorias = $categoriaRepository->findAll(); // Obtiene todas las categorías

        $recetas = $recetaRepository->createQueryBuilder('r')
            ->innerJoin('r.categoria', 'c')
            ->leftJoin('r.pasos', 'p')
            ->addSelect('p')
            ->where('r.visible = :visible')
            ->andWhere('c.nombre = :categoria')
            ->setParameter('visible', 'si')
            ->setParameter('categoria', $categoria)
            ->getQuery()
            ->getResult();

        return $this->render('vista_receta/lista.html.twig', [
            'recetas' => $recetas,
            'categorias' => $categorias, // Pasamos las categorías a la vista
        ]);
    }


    #[Route('/vista/receta/todas', name: 'app_vista_receta_todas', methods: ['GET'])]
    public function todas(RecetaRepository $recetaRepository, CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll(); // Obtener todas las categorías

        $recetas = $recetaRepository->createQueryBuilder('r')
            ->leftJoin('r.categoria', 'c') // Usar 'categoria' en singular
            ->leftJoin('r.pasos', 'p')
            ->addSelect('p', 'c')
            ->where('r.visible = :visible')
            ->setParameter('visible', 'si')
            ->getQuery()
            ->getResult();

        return $this->render('vista_receta/todas.html.twig', [
            'recetas' => $recetas,
            'categorias' => $categorias,
        ]);
    }


}
