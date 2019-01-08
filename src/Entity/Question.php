<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private $piecejointe;

    

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partie", inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $partie;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReponsePossible", mappedBy="question", cascade={"remove"})
     */
    private $reponsespossible;

    

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
        $this->reponsecorrect = new ArrayCollection();
        $this->reponsespossible = new ArrayCollection();
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

    public function getPiecejointe(): ?string
    {
        return $this->piecejointe;
    }

    public function setPiecejointe(?string $piecejointe): self
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

    
}
