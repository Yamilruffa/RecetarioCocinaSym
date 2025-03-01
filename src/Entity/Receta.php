<?php

namespace App\Entity;

use App\Repository\RecetaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecetaRepository::class)]
class Receta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $png = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $tiempoprep = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $porciones = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $dificultad = null;

    #[ORM\ManyToMany(targetEntity: Categoria::class, inversedBy: 'recetas')]
    private Collection $categoria;

    #[ORM\OneToMany(targetEntity: Paso::class, mappedBy: 'receta')]
    private Collection $pasos;

    #[ORM\ManyToMany(targetEntity: Ingrediente::class, inversedBy: 'recetas')]
    private Collection $ingredientes;

    #[ORM\ManyToOne(inversedBy: 'recetas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    #[ORM\OneToMany(targetEntity: Calificacion::class, mappedBy: 'receta')]
    private Collection $calificacions;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $visible = null;

    public function __construct()
    {
        $this->categoria = new ArrayCollection();
        $this->pasos = new ArrayCollection();
        $this->ingredientes = new ArrayCollection();
        $this->calificacions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPng(): ?string
    {
        return $this->png;
    }

    public function setPng(?string $png): static
    {
        $this->png = $png;

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

    public function getTiempoprep(): ?string
    {
        return $this->tiempoprep;
    }

    public function setTiempoprep(?string $tiempoprep): static
    {
        $this->tiempoprep = $tiempoprep;

        return $this;
    }

    public function getPorciones(): ?string
    {
        return $this->porciones;
    }

    public function setPorciones(?string $porciones): static
    {
        $this->porciones = $porciones;

        return $this;
    }

    public function getDificultad(): ?string
    {
        return $this->dificultad;
    }

    public function setDificultad(?string $dificultad): static
    {
        $this->dificultad = $dificultad;

        return $this;
    }

    /**
     * @return Collection<int, Categoria>
     */
    public function getCategoria(): Collection
    {
        return $this->categoria;
    }

    public function addCategorium(Categoria $categorium): static
    {
        if (!$this->categoria->contains($categorium)) {
            $this->categoria->add($categorium);
        }

        return $this;
    }

    public function removeCategorium(Categoria $categorium): static
    {
        $this->categoria->removeElement($categorium);

        return $this;
    }

    /**
     * @return Collection<int, Paso>
     */
    public function getPasos(): Collection
    {
        return $this->pasos;
    }

    public function addPaso(Paso $paso): static
    {
        if (!$this->pasos->contains($paso)) {
            $this->pasos->add($paso);
            $paso->setReceta($this);
        }

        return $this;
    }

    public function removePaso(Paso $paso): static
    {
        if ($this->pasos->removeElement($paso)) {
            // set the owning side to null (unless already changed)
            if ($paso->getReceta() === $this) {
                $paso->setReceta(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ingrediente>
     */
    public function getIngredientes(): Collection
    {
        return $this->ingredientes;
    }

    public function addIngrediente(Ingrediente $ingrediente): static
    {
        if (!$this->ingredientes->contains($ingrediente)) {
            $this->ingredientes->add($ingrediente);
        }

        return $this;
    }

    public function removeIngrediente(Ingrediente $ingrediente): static
    {
        $this->ingredientes->removeElement($ingrediente);

        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection<int, Calificacion>
     */
    public function getCalificacions(): Collection
    {
        return $this->calificacions;
    }

    public function addCalificacion(Calificacion $calificacion): static
    {
        if (!$this->calificacions->contains($calificacion)) {
            $this->calificacions->add($calificacion);
            $calificacion->setReceta($this);
        }

        return $this;
    }

    public function removeCalificacion(Calificacion $calificacion): static
    {
        if ($this->calificacions->removeElement($calificacion)) {
            // set the owning side to null (unless already changed)
            if ($calificacion->getReceta() === $this) {
                $calificacion->setReceta(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->id ?? 'Sin id';
    }

    public function getVisible(): ?string
    {
        return $this->visible;
    }

    public function setVisible(?string $visible): static
    {
        $this->visible = $visible;

        return $this;
    }
}
