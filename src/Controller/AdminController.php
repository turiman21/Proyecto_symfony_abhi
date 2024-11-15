<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\Producto;
use App\Entity\User;
use App\Form\ProductoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $productos = $entityManager->getRepository(Producto::class)->findAll();
        $usuarios = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'titulo' => 'Panel de Administración',
            'productos' => $productos,
            'usuarios' => $usuarios,
        ]);
    }

    #[Route('/admin/productos/nuevo', name: 'admin_nuevo_producto', methods: ['GET', 'POST'])]
    public function newProducto(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $producto = new Producto();
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagen = $form->get('imagen')->getData();

            if ($imagen) {
                $originalFilename = pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imagen->guessExtension();

                try {
                    $imagen->move(
                        $this->getParameter('imagenes_directory'),
                        $newFilename
                    );
                    $producto->setImagen($this->getParameter('imagenes_base_url') . '/' . $newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error al subir la imagen: ' . $e->getMessage());
                }
            }

            $entityManager->persist($producto);
            $entityManager->flush();

            $this->addFlash('success', 'Producto agregado exitosamente.');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/productos/nuevo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/productos/{id}/editar', name: 'admin_editar_producto', methods: ['GET', 'POST'])]
    public function editProducto(Producto $producto, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagen = $form->get('imagen')->getData();

            if ($imagen) {
                $originalFilename = pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imagen->guessExtension();

                try {
                    $imagen->move(
                        $this->getParameter('imagenes_directory'),
                        $newFilename
                    );
                    $producto->setImagen($this->getParameter('imagenes_base_url') . '/' . $newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Hubo un error al subir la imagen.');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Producto actualizado exitosamente.');

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/productos/editar.html.twig', [
            'form' => $form->createView(),
            'producto' => $producto,
        ]);
    }

    #[Route('/admin/productos/{id}/eliminar', name: 'admin_eliminar_producto', methods: ['POST'])]
    public function deleteProducto(Request $request, Producto $producto, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $producto->getId(), $request->request->get('_token'))) {
            $entityManager->remove($producto);
            $entityManager->flush();
            $this->addFlash('success', 'Producto eliminado exitosamente.');
        } else {
            $this->addFlash('danger', 'No se pudo eliminar el producto.');
        }

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/usuario/{id}/editar', name: 'admin_editar_usuario', methods: ['GET', 'POST'])]
    public function editarUsuario(User $usuario, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Usuario actualizado exitosamente.');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/usuarios/editar.html.twig', [
            'form' => $form->createView(),
            'usuario' => $usuario,
        ]);
    }

    #[Route('/admin/usuario/{id}/eliminar', name: 'admin_eliminar_usuario', methods: ['POST'])]
    public function eliminarUsuario(Request $request, User $usuario, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $usuario->getId(), $request->request->get('_token'))) {
            $entityManager->remove($usuario);
            $entityManager->flush();
            $this->addFlash('success', 'Usuario eliminado exitosamente.');
        } else {
            $this->addFlash('danger', 'Error al eliminar el usuario.');
        }

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/reportes', name: 'admin_reportes', methods: ['GET'])]
    public function reportes(): Response
    {
        return $this->render('admin/reportes.html.twig', [
            'titulo' => 'Reportes de Administración',
        ]);
    }

    #[Route('/admin/configuracion', name: 'admin_configuracion', methods: ['GET', 'POST'])]
    public function configuracion(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/configuracion.html.twig', [
            'titulo' => 'Configuración del Sistema',
        ]);
    }



    public function gestionarProductos(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = $em->createQuery('SELECT p FROM App\Entity\Producto p')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($query, true);
        $productos = $paginator->getIterator();

        return $this->render('admin/gestion_productos.html.twig', [
            'productos' => $productos,
            'page' => $page,
            'totalPages' => ceil(count($paginator) / $limit)
        ]);
    }
}
