<?php

namespace App\Entity;

use App\Repository\AlquilerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlquilerRepository::class)]
class Alquiler
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]

    private ?float $precio = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha_alquiler = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha_devolucion = null;

    #[ORM\ManyToOne(inversedBy: 'alquilers')]
    private ?Producto $id_producto = null;

    #[ORM\ManyToOne(inversedBy: 'alquilers')]
    private ?User $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): static
    {
        $this->precio = $precio;

        return $this;
    }

    public function getFechaAlquiler(): ?\DateTimeInterface
    {
        return $this->fecha_alquiler;
    }

    public function setFechaAlquiler(\DateTimeInterface $fecha_alquiler): static
    {
        $this->fecha_alquiler = $fecha_alquiler;

        return $this;
    }

    public function getFechaDevolucion(): ?\DateTimeInterface
    {
        return $this->fecha_devolucion;
    }

    public function setFechaDevolucion(\DateTimeInterface $fecha_devolucion): static
    {
        $this->fecha_devolucion = $fecha_devolucion;

        return $this;
    }

    public function getIdProducto(): ?Producto
    {
        return $this->id_producto;
    }

    public function setIdProducto(?Producto $id_producto): static
    {
        $this->id_producto = $id_producto;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
}
