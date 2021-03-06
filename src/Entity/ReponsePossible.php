<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponsePossibleRepository")
 */
class ReponsePossible
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(maxSize = "60000000000")
     */
    private $piecejointe;



    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Reponse", mappedBy="reponsedonnees")
     */
    private $reponses;



    /**
     * @ORM\Column(type="boolean")
     */
    private $correct;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="reponsespossible")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    /**
     * @ORM\Column(type="float")
     */
    private $fontsize;



    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function __clone() {
      if ($this->id) {
        $this->id = null;

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

    public function getPiecejointe()
    {
        return $this->piecejointe;
    }

    public function setPiecejointe( $piecejointe)
    {
        $this->piecejointe = $piecejointe;

        return $this;
    }


    /**
     * @return Collection|Reponse[]
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->addReponsedonnee($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->contains($reponse)) {
            $this->reponses->removeElement($reponse);
            $reponse->removeReponsedonnee($this);
        }

        return $this;
    }



    public function getCorrect(): ?bool
    {
        return $this->correct;
    }

    public function setCorrect(bool $correct): self
    {
        $this->correct = $correct;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

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

}
