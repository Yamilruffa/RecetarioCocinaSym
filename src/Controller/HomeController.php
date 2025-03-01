<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//
use App\Entity\Receta;
use App\Form\RecetaType;
use App\Repository\RecetaRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(RecetaRepository $recetaRepository, CacheInterface $cache): Response
    {
        // Obtener todas las recetas visibles
        $recetas = $recetaRepository->createQueryBuilder('r')
            ->where('r.visible = :visible')
            ->setParameter('visible', 'si')
            ->getQuery()
            ->getResult();

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
}
