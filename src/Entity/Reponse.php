<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponseRepository")
 */
class Reponse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="reponses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="reponses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ReponsePossible", inversedBy="reponses")
     */
    private $reponsedonnees;

    public function __construct()
    {
        $this->reponsedonnees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

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

    /**
     * @return Collection|ReponsePossible[]
     */
    public function getReponsedonnees(): Collection
    {
        return $this->reponsedonnees;
    }

    public function addReponsedonnee(ReponsePossible $reponsedonnee): self
    {
        if (!$this->reponsedonnees->contains($reponsedonnee)) {
            $this->reponsedonnees[] = $reponsedonnee;
        }

        return $this;
    }

    public function removeReponsedonnee(ReponsePossible $reponsedonnee): self
    {
        if ($this->reponsedonnees->contains($reponsedonnee)) {
            $this->reponsedonnees->removeElement($reponsedonnee);
        }

        return $this;
    }
}
