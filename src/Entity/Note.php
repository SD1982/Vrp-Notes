<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NoteRepository")
 */
class Note
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     * @Assert\Type(
     *     type="float",
     *     message="Cette valeur n'est pas valable."
     * )
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $scan;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=255, minMessage="3 caractères minimum et 255 maximum !!")
     */
    private $adress;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(
     *     type="integer",
     *     message="Cette valeur n'est pas valable."
     * )
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=255, minMessage="3 caractères minimum et 255 maximum !!")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Country()
     */
    private $country;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=8, max=255, minMessage="8 caractères minimum et 255 maximum !!")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId() : ? int
    {
        return $this->id;
    }

    public function getDate() : ? \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date) : self
    {
        $this->date = $date;

        return $this;
    }

    public function getMontant() : ? float
    {
        return $this->montant;
    }

    public function setMontant(float $montant) : self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getType() : ? string
    {
        return $this->type;
    }

    public function setType(string $type) : self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatut() : ? string
    {
        return $this->statut;
    }

    public function setStatut(string $statut) : self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getScan() : ? string
    {
        return $this->scan;
    }

    public function setScan(? string $scan) : self
    {
        $this->scan = $scan;

        return $this;
    }

    public function getAdress() : ? string
    {
        return $this->adress;
    }

    public function setAdress(string $adress) : self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostcode() : ? int
    {
        return $this->postcode;
    }

    public function setPostcode(int $postcode) : self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity() : ? string
    {
        return $this->city;
    }

    public function setCity(string $city) : self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry() : ? string
    {
        return $this->country;
    }

    public function setCountry(string $country) : self
    {
        $this->country = $country;

        return $this;
    }

    public function getLatitude() : ? float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude) : self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude() : ? float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude) : self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDescription() : ? string
    {
        return $this->description;
    }

    public function setDescription(? string $description) : self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt() : ? \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser() : ? User
    {
        return $this->user;
    }

    public function setUser(? User $user) : self
    {
        $this->user = $user;

        return $this;
    }

}
