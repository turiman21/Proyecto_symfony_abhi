<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, Venta>
     */
    #[ORM\OneToMany(targetEntity: Venta::class, mappedBy: 'id_user')]
    private Collection $ventas;

    /**
     * @var Collection<int, Alquiler>
     */
    #[ORM\OneToMany(targetEntity: Alquiler::class, mappedBy: 'id_user')]
    private Collection $alquilers;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellido1 = null;

    #[ORM\Column(length: 255)]
    private ?string $apellido2 = null;

    #[ORM\Column(length: 255)]
    private ?string $direcccion = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var Collection<int, Reparacion>
     */
    #[ORM\OneToMany(targetEntity: Reparacion::class, mappedBy: 'id_user')]
    private Collection $reparacions;

    public function __construct()
    {
        $this->ventas = new ArrayCollection();
        $this->alquilers = new ArrayCollection();
        $this->reparacions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Venta>
     */
    public function getVentas(): Collection
    {
        return $this->ventas;
    }

    public function addVenta(Venta $venta): static
    {
        if (!$this->ventas->contains($venta)) {
            $this->ventas->add($venta);
            $venta->setIdUser($this);
        }

        return $this;
    }

    public function removeVenta(Venta $venta): static
    {
        if ($this->ventas->removeElement($venta)) {
            // set the owning side to null (unless already changed)
            if ($venta->getIdUser() === $this) {
                $venta->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Alquiler>
     */
    public function getAlquilers(): Collection
    {
        return $this->alquilers;
    }

    public function addAlquiler(Alquiler $alquiler): static
    {
        if (!$this->alquilers->contains($alquiler)) {
            $this->alquilers->add($alquiler);
            $alquiler->setIdUser($this);
        }

        return $this;
    }

    public function removeAlquiler(Alquiler $alquiler): static
    {
        if ($this->alquilers->removeElement($alquiler)) {
            // set the owning side to null (unless already changed)
            if ($alquiler->getIdUser() === $this) {
                $alquiler->setIdUser(null);
            }
        }

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido1(): ?string
    {
        return $this->apellido1;
    }

    public function setApellido1(string $apellido1): static
    {
        $this->apellido1 = $apellido1;

        return $this;
    }

    public function getApellido2(): ?string
    {
        return $this->apellido2;
    }

    public function setApellido2(string $apellido2): static
    {
        $this->apellido2 = $apellido2;

        return $this;
    }

    public function getDirecccion(): ?string
    {
        return $this->direcccion;
    }

    public function setDirecccion(string $direcccion): static
    {
        $this->direcccion = $direcccion;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Reparacion>
     */
    public function getReparacions(): Collection
    {
        return $this->reparacions;
    }

    public function addReparacion(Reparacion $reparacion): static
    {
        if (!$this->reparacions->contains($reparacion)) {
            $this->reparacions->add($reparacion);
            $reparacion->setIdUser($this);
        }

        return $this;
    }

    public function removeReparacion(Reparacion $reparacion): static
    {
        if ($this->reparacions->removeElement($reparacion)) {
            // set the owning side to null (unless already changed)
            if ($reparacion->getIdUser() === $this) {
                $reparacion->setIdUser(null);
            }
        }

        return $this;
    }
}
