<?php

namespace App\Entity;

use App\Repository\ProductoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductoRepository::class)]
class Producto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombreProducto = null; // Eliminada la duplicación

    #[ORM\Column]
    private ?float $precio_venta = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = null;

    #[ORM\Column]
    private ?float $precio_alquiler = null;

    // Campo tipo (json) para almacenar un array u objeto JSON
    #[ORM\Column(type: 'json', nullable: true)]
    private array $tipo = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imagen = null;


    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stock = null;


    /**
     * Relación con la entidad Alquiler (Uno a muchos)
     *
     * @var Collection<int, Alquiler>
     */
    #[ORM\OneToMany(targetEntity: Alquiler::class, mappedBy: 'id_producto')]
    private Collection $alquilers;

    #[ORM\ManyToOne(inversedBy: 'productos')]
    private ?Venta $id_venta = null;

    /**
     * Relación con la entidad Reparacion (Uno a muchos)
     *
     * @var Collection<int, Reparacion>
     */
    #[ORM\OneToMany(targetEntity: Reparacion::class, mappedBy: 'id_producto')]
    private Collection $reparacions;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255)]
    private ?string $marca = null;

    public function __construct()
    {
        $this->alquilers = new ArrayCollection();
        $this->reparacions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreProducto(): ?string
    {
        return $this->nombreProducto;
    }

    public function setNombreProducto(string $nombreProducto): self
    {
        $this->nombreProducto = $nombreProducto;
        return $this;
    }

    public function getPrecioVenta(): ?float
    {
        return $this->precio_venta;
    }

    public function setPrecioVenta(float $precio_venta): static
    {
        $this->precio_venta = $precio_venta;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getPrecioAlquiler(): ?float
    {
        return $this->precio_alquiler;
    }

    public function setPrecioAlquiler(float $precio_alquiler): static
    {
        $this->precio_alquiler = $precio_alquiler;

        return $this;
    }

    public function getTipo(): ?array
    {
        return $this->tipo;
    }

    public function setTipo(?array $tipo): self
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;
        return $this;
    }

    public function getAlquilers(): Collection
    {
        return $this->alquilers;
    }

    public function addAlquiler(Alquiler $alquiler): static
    {
        if (!$this->alquilers->contains($alquiler)) {
            $this->alquilers->add($alquiler);
            $alquiler->setIdProducto($this);
        }

        return $this;
    }

    public function removeAlquiler(Alquiler $alquiler): static
    {
        if ($this->alquilers->removeElement($alquiler)) {
            if ($alquiler->getIdProducto() === $this) {
                $alquiler->setIdProducto(null);
            }
        }

        return $this;
    }

    public function getIdVenta(): ?Venta
    {
        return $this->id_venta;
    }

    public function setIdVenta(?Venta $id_venta): static
    {
        $this->id_venta = $id_venta;
        return $this;
    }

    public function getReparacions(): Collection
    {
        return $this->reparacions;
    }

    public function addReparacion(Reparacion $reparacion): static
    {
        if (!$this->reparacions->contains($reparacion)) {
            $this->reparacions->add($reparacion);
            $reparacion->setIdProducto($this);
        }

        return $this;
    }

    public function removeReparacion(Reparacion $reparacion): static
    {
        if ($this->reparacions->removeElement($reparacion)) {
            if ($reparacion->getIdProducto() === $this) {
                $reparacion->setIdProducto(null);
            }
        }

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(string $marca): static
    {
        $this->marca = $marca;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }
}
