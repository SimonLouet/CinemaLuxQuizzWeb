<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PartieRepository")
 */
class Partie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Utilisateur", mappedBy="parties")
     */
    private $utilisateurs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="partie")
     */
    private $questions;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */

    private $imagefondname;



    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $theme;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $colortext;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $colortitre;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $colorchrono;




    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|Utilisateur[]
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
            $utilisateur->addParty($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->removeElement($utilisateur);
            $utilisateur->removeParty($this);
        }

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setPartie($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getPartie() === $this) {
                $question->setPartie(null);
            }
        }

        return $this;
    }



    public function getImagefondname()
    {
        return $this->imagefondname;
    }

    public function setImagefondname( $imagefondname)
    {
        $this->imagefondname = $imagefondname;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getColortext(): ?string
    {
        return $this->colortext;
    }

    public function setColortext(string $colortext): self
    {
        $this->colortext = $colortext;

        return $this;
    }

    public function getColortitre(): ?string
    {
        return $this->colortitre;
    }

    public function setColortitre(string $colortitre): self
    {
        $this->colortitre = $colortitre;

        return $this;
    }

    public function getColorchrono(): ?string
    {
        return $this->colorchrono;
    }

    public function setColorchrono(string $colorchrono): self
    {
        $this->colorchrono = $colorchrono;

        return $this;
    }




}
