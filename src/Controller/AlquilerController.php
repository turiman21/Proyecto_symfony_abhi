<?php

namespace App\Controller;

use App\Entity\Alquiler;
use App\Form\AlquilerType;
use App\Repository\AlquilerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/alquiler')]
final class AlquilerController extends AbstractController
{
    #[Route(name: 'app_alquiler_index', methods: ['GET'])]
    public function index(AlquilerRepository $alquilerRepository): Response
    {
        return $this->render('alquiler/index.html.twig', [
            'alquilers' => $alquilerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_alquiler_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $alquiler = new Alquiler();
        $form = $this->createForm(AlquilerType::class, $alquiler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($alquiler);
            $entityManager->flush();

            return $this->redirectToRoute('app_alquiler_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('alquiler/new.html.twig', [
            'alquiler' => $alquiler,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_alquiler_show', methods: ['GET'])]
    public function show(Alquiler $alquiler): Response
    {
        return $this->render('alquiler/show.html.twig', [
            'alquiler' => $alquiler,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_alquiler_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Alquiler $alquiler, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AlquilerType::class, $alquiler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_alquiler_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('alquiler/edit.html.twig', [
            'alquiler' => $alquiler,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_alquiler_delete', methods: ['POST'])]
    public function delete(Request $request, Alquiler $alquiler, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$alquiler->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($alquiler);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_alquiler_index', [], Response::HTTP_SEE_OTHER);
    }
}
