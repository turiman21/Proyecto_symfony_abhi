<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Form\ProductoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductoController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;

    
    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    #[Route('/admin/productos/smartphones-consolas', name: 'admin_productos_smartphones_consolas', methods: ['GET'])]
    public function getSmartphonesYConsolas(): JsonResponse
    {
        $productosSmartphones = $this->getProductosPorTipo('smartphone');
        $productosConsolas = $this->getProductosPorTipo('consola');

        $productos = array_merge($productosSmartphones, $productosConsolas);

        return new JsonResponse($productos);
    }

    #[Route('/admin/productos/videojuegos', name: 'admin_productos_videojuegos', methods: ['GET'])]
    public function getVideojuegos(): JsonResponse
    {
        return new JsonResponse($this->getProductosPorTipo('videojuego'));
    }

    #[Route('/admin/productos/smartphones', name: 'admin_productos_smartphones', methods: ['GET'])]
    public function getSmartphones(): JsonResponse
    {
        return new JsonResponse($this->getProductosPorTipo('smartphone'));
    }

    #[Route('/admin/productos/consolas', name: 'admin_productos_consolas', methods: ['GET'])]
    public function getConsolas(): JsonResponse
    {
        return new JsonResponse($this->getProductosPorTipo('consola'));
    }

    #[Route('/admin/productos/consolas-smartphones', name: 'admin_productos_consolas_smartphones', methods: ['GET'])]
    public function getConsolasYSmartphones(): JsonResponse
    {
        $productosConsolas = $this->getProductosPorTipo('consola');
        $productosSmartphones = $this->getProductosPorTipo('smartphone');

        $productos = array_merge($productosConsolas, $productosSmartphones);

        return new JsonResponse($productos);
    }

    #[Route('/admin/producto/{id}', name: 'admin_producto_ver', methods: ['GET'])]
    public function getProductoById(int $id): JsonResponse
    {
        $producto = $this->entityManager->getRepository(Producto::class)->find($id);

        if (!$producto) {
            return new JsonResponse(['error' => 'Producto no encontrado'], 404);
        }

        $productoData = [
            'id' => $producto->getId(),
            'nombreProducto' => $producto->getNombreProducto(),
            'precioVenta' => $producto->getPrecioVenta(),
            'estado' => $producto->getEstado(),
            'precioAlquiler' => $producto->getPrecioAlquiler(),
            'tipo' => $producto->getTipo(),
            'imagenes' => $producto->getImagen() ? $this->ensureAbsoluteUrl($producto->getImagen()) : null,
            'descripcion' => $producto->getDescripcion(),
            'marca' => $producto->getMarca(),
        ];

        return new JsonResponse($productoData);
    }

    #[Route('/producto/nuevo', name: 'producto_nuevo', methods: ['POST'])]
    public function nuevo(Request $request): JsonResponse
    {
        $producto = new Producto();
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagen = $form->get('imagen')->getData();

            if ($imagen) {
                $originalFilename = pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imagen->guessExtension();

                try {
                    $imagen->move($this->getParameter('imagenes_directory'), $newFilename);
                    $producto->setImagen('/uploads/' . $newFilename);
                } catch (\Exception $e) {
                    return new JsonResponse(['error' => 'Error al subir la imagen'], 500);
                }
            }

            $this->entityManager->persist($producto);
            $this->entityManager->flush();

            return new JsonResponse(['success' => 'Producto creado con éxito'], 201);
        }

        return new JsonResponse(['error' => 'Error al crear el producto'], 400);
    }

    #[Route('/admin/productos/nombres-ids-tipos-consolas-smartphones', name: 'admin_productos_nombres_ids_tipos_consolas_smartphones', methods: ['GET'])]
    public function getNombresIdsTiposConsolasSmartphones(): JsonResponse
    {
        $productosConsolas = $this->getProductosPorTipoSimplificado('consola');
        $productosSmartphones = $this->getProductosPorTipoSimplificado('smartphone');

        $productos = array_merge($productosConsolas, $productosSmartphones);

        return new JsonResponse($productos);
    }
    

    private function getProductosPorTipo(string $tipoBuscado): array
    {
        $connection = $this->entityManager->getConnection();

        $sql = "SELECT * FROM producto WHERE tipo::jsonb @> :tipo";
        $statement = $connection->prepare($sql);
        $resultSet = $statement->executeQuery(['tipo' => json_encode([$tipoBuscado])]);

        $productos = $resultSet->fetchAllAssociative();

        return array_map(function ($producto) {
            $producto['imagen'] = $producto['imagen'] ? $this->ensureAbsoluteUrl($producto['imagen']) : null;
            return $producto;
        }, $productos);
    }
    #[Route('/admin/productos/search', name: 'admin_productos_search', methods: ['GET'])]
public function searchProducts(Request $request): JsonResponse
{
    $search = $request->query->get('query', '');
    $productosConsolas = $this->getProductosPorTipoSimplificado('consola', $search);
    $productosSmartphones = $this->getProductosPorTipoSimplificado('smartphone', $search);

    $productos = array_merge($productosConsolas, $productosSmartphones);

    return new JsonResponse($productos);
}

private function getProductosPorTipoSimplificado(string $tipoBuscado, string $search = ''): array
{
    $connection = $this->entityManager->getConnection();

    $sql = "SELECT id, nombre_producto AS nombreProducto, tipo FROM producto WHERE tipo::jsonb @> :tipo AND nombre_producto ILIKE :search";
    $statement = $connection->prepare($sql);
    $resultSet = $statement->executeQuery(['tipo' => json_encode([$tipoBuscado]), 'search' => '%' . $search . '%']);

    return array_map(function ($producto) {
        return [
            'id' => $producto['id'],
            'nombreProducto' => $producto['nombreProducto'],
            'tipo' => $producto['tipo'],
        ];
    }, $resultSet->fetchAllAssociative());
}

private function ensureAbsoluteUrl($imagePath): string
{
    return strpos($imagePath, 'http') === 0 ? $imagePath : 'https://tu-dominio.com' . $imagePath;
}



    #[Route('/uploads/{filename}', name: 'serve_image', methods: ['GET'])]
    public function serveImage(string $filename): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('La imagen no se encuentra.');
        }

        return new Response(file_get_contents($filePath), 200, [
            'Content-Type' => mime_content_type($filePath),
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

}
