<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $libelle;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $ouverte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(maxSize = "60000000000")
     */
    private $piecejointe;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partie", inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $partie;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReponsePossible", mappedBy="question", cascade={"remove","persist"})
     */
    private $reponsespossible;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * @ORM\Column(type="integer")
     */
    private $timer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $videoyoutube;

    /**
     * @ORM\Column(type="float")
     */
    private $fontsize;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cadeau;



    public function __construct()
    {
        $this->reponses = new ArrayCollection();
        $this->reponsecorrect = new ArrayCollection();
        $this->reponsespossible = new ArrayCollection();
    }

    public function __clone() {
      if ($this->id) {
        $this->id = null;
        // cloning the relation M which is a OneToMany
        $reponsespossibleClone = new ArrayCollection();
        foreach ($this->reponsespossible as $item) {
          $itemClone = clone $item;
          $itemClone->setQuestion($this);
          $reponsespossibleClone->add($itemClone);
        }
        $this->reponsespossible = $reponsespossibleClone;
      }
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getOuverte(): ?bool
    {
        return $this->ouverte;
    }

    public function setOuverte(bool $ouverte): self
    {
        $this->ouverte = $ouverte;

        return $this;
    }

    public function getPiecejointe()
    {
        return $this->piecejointe;
    }

    public function setPiecejointe($piecejointe)
    {
        $this->piecejointe = $piecejointe;

        return $this;
    }



    public function getPartie(): ?Partie
    {
        return $this->partie;
    }

    public function setPartie(?Partie $partie): self
    {
        $this->partie = $partie;

        return $this;
    }

    /**
     * @return Collection|ReponsePossible[]
     */
    public function getReponsespossible(): Collection
    {
        return $this->reponsespossible;
    }

    public function addReponsespossible(ReponsePossible $reponsespossible): self
    {
        if (!$this->reponsespossible->contains($reponsespossible)) {
            $this->reponsespossible[] = $reponsespossible;
            $reponsespossible->setQuestion($this);
        }

        return $this;
    }

    public function removeReponsespossible(ReponsePossible $reponsespossible): self
    {
        if ($this->reponsespossible->contains($reponsespossible)) {
            $this->reponsespossible->removeElement($reponsespossible);
            // set the owning side to null (unless already changed)
            if ($reponsespossible->getQuestion() === $this) {
                $reponsespossible->setQuestion(null);
            }
        }

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getTimer(): ?int
    {
        return $this->timer;
    }

    public function setTimer(int $timer): self
    {
        $this->timer = $timer;

        return $this;
    }

    public function getVideoyoutube(): ?string
    {
        return $this->videoyoutube;
    }

    public function setVideoyoutube(?string $videoyoutube): self
    {
        $this->videoyoutube = $videoyoutube;

        return $this;
    }

    public function getFontsize(): ?float
    {
        return $this->fontsize;
    }

    public function setFontsize(float $fontsize): self
    {
        $this->fontsize = $fontsize;

        return $this;
    }

    public function getCadeau(): ?string
    {
        return $this->cadeau;
    }

    public function setCadeau(?string $cadeau): self
    {
        $this->cadeau = $cadeau;

        return $this;
    }


}
