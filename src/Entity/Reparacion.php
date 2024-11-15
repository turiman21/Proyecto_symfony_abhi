<?php

namespace App\Entity;

use App\Repository\ReparacionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReparacionRepository::class)]
class Reparacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $estado_reparacion = null;

    #[ORM\Column]
    private ?float $costo_reparacion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha_entrada = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha_salida = null;

    #[ORM\ManyToOne(inversedBy: 'reparacions')]
    private ?Producto $id_producto = null;

    #[ORM\ManyToOne(inversedBy: 'reparacions')]
    private ?User $id_user = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcionUsuario = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notaTecnico = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstadoReparacion(): ?string
    {
        return $this->estado_reparacion;
    }

    public function setEstadoReparacion(string $estado_reparacion): static
    {
        $this->estado_reparacion = $estado_reparacion;

        return $this;
    }

    public function getCostoReparacion(): ?float
    {
        return $this->costo_reparacion;
    }

    public function setCostoReparacion(float $costo_reparacion): static
    {
        $this->costo_reparacion = $costo_reparacion;

        return $this;
    }

    public function getFechaEntrada(): ?\DateTimeInterface
    {
        return $this->fecha_entrada;
    }

    public function setFechaEntrada(\DateTimeInterface $fecha_entrada): static
    {
        $this->fecha_entrada = $fecha_entrada;

        return $this;
    }

    public function getFechaSalida(): ?\DateTimeInterface
    {
        return $this->fecha_salida;
    }

    public function setFechaSalida(\DateTimeInterface $fecha_salida): static
    {
        $this->fecha_salida = $fecha_salida;

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
    public function getDescripcionUsuario(): ?string
    {
        return $this->descripcionUsuario;
    }

    public function setDescripcionUsuario(?string $descripcionUsuario): self
    {
        $this->descripcionUsuario = $descripcionUsuario;

        return $this;
    }


    public function getNotaTecnico(): ?string
    {
        return $this->notaTecnico;
    }

    public function setNotaTecnico(?string $notaTecnico): self
    {
        $this->notaTecnico = $notaTecnico;
        return $this;
    }

}
