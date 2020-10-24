<?php

namespace App\Entity;

use App\Repository\ColisProductsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ColisProductsRepository::class)
 */
class ColisProducts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"colis_details"})
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Colis", inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $colis;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"colis_details"})
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getColis(): ?Colis
    {
        return $this->colis;
    }

    public function setColis(?Colis $colis): self
    {
        $this->colis = $colis;

        return $this;
    }

    public function getProduct(): ?Produit
    {
        return $this->product;
    }

    public function setProduct(?Produit $product): self
    {
        $this->product = $product;

        return $this;
    }
}
