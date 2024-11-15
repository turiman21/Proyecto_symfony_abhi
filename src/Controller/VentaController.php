<?php

namespace App\Controller;

use App\Entity\Venta;
use App\Form\VentaType;
use App\Repository\VentaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/venta')]
final class VentaController extends AbstractController
{
    #[Route(name: 'app_venta_index', methods: ['GET'])]
    public function index(VentaRepository $ventaRepository): Response
    {
        return $this->render('venta/index.html.twig', [
            'ventas' => $ventaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_venta_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProductoRepository $productoRepository): Response
    {
        $user = $this->getUser();
        $cartData = json_decode($request->getContent(), true);

        $ventum = new Venta();
        $ventum->setUser($user);

        // Añadir productos de la cesta a la venta
        foreach ($cartData['productos'] as $productoData) {
            $producto = $productoRepository->find($productoData['id']);
            if ($producto) {
                $ventum->addProducto($producto);
            }
        }

        $entityManager->persist($ventum);
        $entityManager->flush();

        return $this->json(['message' => 'Venta creada con éxito']);
    }


    #[Route('/{id}', name: 'app_venta_show', methods: ['GET'])]
    public function show(Venta $ventum): Response
    {
        return $this->render('venta/show.html.twig', [
            'ventum' => $ventum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_venta_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Venta $ventum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VentaType::class, $ventum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_venta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('venta/edit.html.twig', [
            'ventum' => $ventum,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_venta_delete', methods: ['POST'])]
    public function delete(Request $request, Venta $ventum, EntityManagerInterface $entityManager): Response
    {
        // CSRF token validation with correct token retrieval
        $csrfToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$ventum->getId(), $csrfToken)) {
            $entityManager->remove($ventum);
            $entityManager->flush();

            $this->addFlash('success', 'Venta eliminada exitosamente.');
        } else {
            $this->addFlash('error', 'Token CSRF no válido.');
        }

        return $this->redirectToRoute('app_venta_index', [], Response::HTTP_SEE_OTHER);
    }
}
