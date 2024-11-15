<?php

namespace App\Controller;

use App\Entity\Reparacion;
use App\Entity\Producto;
use App\Form\ReparacionType;
use App\Repository\ReparacionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class ReparacionController extends AbstractController
{
    #[Route(name: 'app_reparacion_index', methods: ['GET'])]
    public function index(ReparacionRepository $reparacionRepository): Response
    {
        return $this->render('reparacion/index.html.twig', [
            'reparacions' => $reparacionRepository->findAll(),
        ]);
    }

    #[Route('api/reparacion/new', name: 'app_reparacion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reparacion = new Reparacion();
        $form = $this->createForm(ReparacionType::class, $reparacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reparacion);
            $entityManager->flush();

            return $this->redirectToRoute('app_reparacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reparacion/new.html.twig', [
            'reparacion' => $reparacion,
            'form' => $form,
        ]);
    }

    #[Route('api/reparacion/{id}', name: 'app_reparacion_show', methods: ['GET'])]
    public function show(Reparacion $reparacion): Response
    {
        return $this->render('reparacion/show.html.twig', [
            'reparacion' => $reparacion,
        ]);
    }

    #[Route('/api/reparacion{id}/edit', name: 'app_reparacion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reparacion $reparacion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReparacionType::class, $reparacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reparacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reparacion/edit.html.twig', [
            'reparacion' => $reparacion,
            'form' => $form,
        ]);
    }

    #[Route('/reparacion/{id<\d+>}', name: 'app_reparacion_delete', methods: ['POST'])]
    public function delete(Request $request, Reparacion $reparacion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reparacion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reparacion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reparacion_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/api/crear-reparacion', name: 'api_crear_reparacion', methods: ['POST'])]
    public function crearReparacion(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger, Security $security): JsonResponse
    {
        try {
            // Obtener el usuario autenticado
            $user = $security->getUser();
            if (!$user instanceof UserInterface) {
                $logger->error('Usuario no autenticado');
                return new JsonResponse(['error' => 'Usuario no autenticado'], JsonResponse::HTTP_UNAUTHORIZED);
            }

            // Decodificar la solicitud JSON
            $data = json_decode($request->getContent(), true);
            if (!isset($data['id_producto'], $data['descripcionUsuario'])) {
                $logger->error('Datos incompletos', $data);
                return new JsonResponse(['error' => 'Datos incompletos'], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Verifica que el id_producto existe en la base de datos
            $producto = $entityManager->getRepository(Producto::class)->find($data['id_producto']);
            if (!$producto) {
                $logger->error('Producto no encontrado', ['id_producto' => $data['id_producto']]);
                return new JsonResponse(['error' => 'Producto no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }

            // Crear la entidad Reparacion
            $reparacion = new Reparacion();
            $reparacion->setIdProducto($producto);
            $reparacion->setDescripcionUsuario($data['descripcionUsuario']);
            $reparacion->setIdUser($user); // Asociar el usuario autenticado
            $reparacion->setEstadoReparacion('pendiente'); // Estado inicial de la reparación

            // Guardar en la base de datos
            $entityManager->persist($reparacion);
            $entityManager->flush();

            $logger->info('Reparación creada exitosamente', ['reparacion_id' => $reparacion->getId()]);
            return new JsonResponse(['status' => 'Reparación creada exitosamente'], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            // Registrar el error detallado
            $logger->error('Error al crear la reparación: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return new JsonResponse(['error' => 'Error interno del servidor. Intente más tarde.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
