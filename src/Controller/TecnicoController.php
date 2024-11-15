<?php

namespace App\Controller;

use App\Entity\Reparacion;
use App\Entity\Producto;
use App\Form\ReparacionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;



#[Route('/tecnico')]
class TecnicoController extends AbstractController
{
    #[Route('/consolas/dashboard', name: 'tecnico_consolas_dashboard')]
    public function tecnicoConsolasDashboard(EntityManagerInterface $entityManager): Response
    {
        $connection = $entityManager->getConnection();

        // Consulta para seleccionar productos con tipo "consola"
        $sql = "SELECT * FROM producto WHERE tipo::jsonb @> :tipo";
        $statement = $connection->prepare($sql);
        $resultSet = $statement->executeQuery(['tipo' => json_encode(["consola"])]);
        $consolasDisponibles = $resultSet->fetchAllAssociative();

        // Obtener el conteo y listado de reparaciones
        $reparacionesPendientes = $entityManager->getRepository(Reparacion::class)->count(['estado_reparacion' => 'pendiente']);
        $reparacionesCompletadas = $entityManager->getRepository(Reparacion::class)->count(['estado_reparacion' => 'completada']);
        $reparaciones = $entityManager->getRepository(Reparacion::class)->findAll();

        return $this->render('tecnico/consolas.html.twig', [
            'reparacionesPendientes' => $reparacionesPendientes,
            'reparacionesCompletadas' => $reparacionesCompletadas,
            'consolasDisponibles' => count($consolasDisponibles),
            'consolas' => $consolasDisponibles,
            'reparaciones' => $reparaciones, // Añadir reparaciones aquí
        ]);
    }




    #[Route('/reparaciones', name: 'tecnico_reparaciones_index')]
    public function reparacionesIndex(EntityManagerInterface $entityManager): Response
    {
        $reparaciones = $entityManager->getRepository(Reparacion::class)->findAll();
        return $this->render('tecnico/consolas/index.html.twig', [
            'reparaciones' => $reparaciones,
        ]);
    }

    #[Route('/reparacion/nueva', name: 'tecnico_reparacion_nueva', methods: ['GET', 'POST'])]
    public function nuevaReparacion(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reparacion = new Reparacion();
        $form = $this->createForm(ReparacionType::class, $reparacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reparacion);
            $entityManager->flush();
            $this->addFlash('success', 'Reparación registrada con éxito.');
            return $this->redirectToRoute('tecnico_consolas_dashboard');
        }

        return $this->render('tecnico/reparaciones/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reparacion/{id}', name: 'tecnico_reparacion_ver', methods: ['GET'])]
    public function verReparacion(Reparacion $reparacion): Response
    {
        return $this->render('tecnico/reparaciones/ver.html.twig', [
            'reparacion' => $reparacion,
        ]);
    }



    #[Route('/reparacion/{id}/editar', name: 'tecnico_reparacion_editar', methods: ['GET', 'POST'])]
    public function editarReparacion(Request $request, Reparacion $reparacion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReparacionType::class, $reparacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Reparación actualizada con éxito.');
            return $this->redirectToRoute('tecnico_consolas_dashboard');
        }

        return $this->render('tecnico/reparaciones/form.html.twig', [
            'form' => $form->createView(),
            'reparacion' => $reparacion,
        ]);
    }
    #[Route('/reparacion/{id}/eliminar', name: 'tecnico_reparacion_eliminar', methods: ['DELETE'])]
    public function eliminarReparacion(Reparacion $reparacion, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
    {
        try {
            $entityManager->remove($reparacion);
            $entityManager->flush();
            return new JsonResponse(['status' => 'success', 'message' => 'Reparación eliminada con éxito.']);
        } catch (\Exception $e) {
            // Registrar el error en los logs
            $logger->error('Error al eliminar la reparación: ' . $e->getMessage());

            // Enviar una respuesta JSON con el error
            return new JsonResponse(['status' => 'error', 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }



    //TELEFONIA



    #[Route('/telefonia/dashboard', name: 'tecnico_telefonia_dashboard')]
    public function tecnicoTelefoniaDashboard(EntityManagerInterface $entityManager): Response
    {
        $connection = $entityManager->getConnection();
        $sql = "SELECT * FROM producto WHERE tipo::jsonb @> :tipo";
        $statement = $connection->prepare($sql);
        $resultSet = $statement->executeQuery(['tipo' => json_encode(["smartphone"])]);
        $smartphonesDisponibles = $resultSet->fetchAllAssociative();

        $reparacionesPendientes = $entityManager->getRepository(Reparacion::class)->count(['estado_reparacion' => 'pendiente']);
        $reparacionesCompletadas = $entityManager->getRepository(Reparacion::class)->count(['estado_reparacion' => 'completada']);
        $reparaciones = $entityManager->getRepository(Reparacion::class)->findAll();

        return $this->render('tecnico/telefonia.html.twig', [
            'reparacionesPendientes' => $reparacionesPendientes,
            'reparacionesCompletadas' => $reparacionesCompletadas,
            'smartphonesDisponibles' => count($smartphonesDisponibles),
            'smartphones' => $smartphonesDisponibles,
            'reparaciones' => $reparaciones,
        ]);
    }

    #[Route('/telefonia/reparacion/nueva', name: 'tecnico_telefonia_reparacion_nueva', methods: ['GET', 'POST'])]
    public function nuevaReparacionTelefonia(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reparacion = new Reparacion();
        $form = $this->createForm(ReparacionType::class, $reparacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reparacion);
            $entityManager->flush();
            $this->addFlash('success', 'Reparación registrada con éxito.');
            return $this->redirectToRoute('tecnico_telefonia_dashboard');
        }

        return $this->render('tecnico/reparaciones/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/telefonia/reparacion/{id}/editar', name: 'tecnico_telefonia_reparacion_editar', methods: ['GET', 'POST'])]
    public function editarReparacionTelefonia(Request $request, Reparacion $reparacion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReparacionType::class, $reparacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Reparación actualizada con éxito.');
            return $this->redirectToRoute('tecnico_telefonia_dashboard');
        }

        return $this->render('tecnico/reparaciones/form.html.twig', [
            'form' => $form->createView(),
            'reparacion' => $reparacion,
        ]);
    }

    #[Route('/telefonia/reparacion/{id}/eliminar', name: 'tecnico_telefonia_reparacion_eliminar', methods: ['DELETE'])]
    public function eliminarReparacionTelefonia(Reparacion $reparacion, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
    {
        try {
            $entityManager->remove($reparacion);
            $entityManager->flush();
            return new JsonResponse(['status' => 'success', 'message' => 'Reparación eliminada con éxito.']);
        } catch (\Exception $e) {
            $logger->error('Error al eliminar la reparación: ' . $e->getMessage());
            return new JsonResponse(['status' => 'error', 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }





}