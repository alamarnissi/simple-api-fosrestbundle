<?php

namespace App\Entity;

use App\Repository\ColisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ColisRepository::class)
 */
class Colis
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ColisProducts", mappedBy="colis", cascade={"persist"})
     * @Groups({"colis_details"})
     */
    protected $products;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", cascade={"persist"})
     * @Groups({"colis_details"})
     */
    protected $client;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"colis_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"colis_details"})
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

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"colis_details"})
     */
    private $reference;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"colis_details"})
     */
    private $Signe;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->date_creation = new \DateTime();
        $this->etat = "1";
        $this->reference = uniqid();
        $this->setSigne();
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

    public function getDateCreation(): string
    {
        return $this->date_creation->format('d-m-Y H:i');
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

    public function getClient(): ?Utilisateur
    {
        return $this->client;
    }

    public function setClient(?Utilisateur $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function addProduct(ColisProducts $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setColis($this);
        }

        return $this;
    }

    public function removeProduct(ColisProducts $product): self
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getSigne(): ?bool
    {
        return $this->Signe;
    }

    public function setSigne(bool $Signe = true): self
    {
        $this->Signe = $Signe;

        return $this;
    }
}
