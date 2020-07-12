<?php

namespace App\Entity;

use App\Repository\MoteurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MoteurRepository::class)
 */
class Moteur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $num_series;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $marque;

    /**
     * @ORM\Column(type="integer")
     */
    private $nBCheveaux;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="moteurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumSeries(): ?string
    {
        return $this->num_series;
    }

    public function setNumSeries(string $num_series): self
    {
        $this->num_series = $num_series;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getNBCheveaux(): ?int
    {
        return $this->nBCheveaux;
    }

    public function setNBCheveaux(int $nBCheveaux): self
    {
        $this->nBCheveaux = $nBCheveaux;

        return $this;
    }

    public function getOwner(): ?client
    {
        return $this->owner;
    }

    public function setOwner(?client $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
