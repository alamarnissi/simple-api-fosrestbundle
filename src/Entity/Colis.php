<?php

namespace App\Entity;

use App\Repository\ColisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ColisRepository::class)
 */
class Colis
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ColisProducts", mappedBy="colis", cascade={"persist"})
     */
    protected $products;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $etat;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_livraison;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_retour;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateLivraison(): ?\DateTimeInterface
    {
        return $this->date_livraison;
    }

    public function setDateLivraison(?\DateTimeInterface $date_livraison): self
    {
        $this->date_livraison = $date_livraison;

        return $this;
    }

    public function getDateRetour(): ?\DateTimeInterface
    {
        return $this->date_retour;
    }

    public function setDateRetour(?\DateTimeInterface $date_retour): self
    {
        $this->date_retour = $date_retour;

        return $this;
    }

    /**
     * @return Collection|Colis_Produit[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Colis_Produit $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setColis($this);
        }

        return $this;
    }

    public function removeProduct(Colis_Produit $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getColis() === $this) {
                $product->setColis(null);
            }
        }

        return $this;
    }
}
