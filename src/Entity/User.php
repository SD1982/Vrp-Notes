<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *      fields={"username"},
 *      message="Cet username est deja utilisé !",
 * )
 * @UniqueEntity(
 *      fields={"email"},
 *      message="Cet email est deja utilisé !",
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, max=50, minMessage="8 caractères minimum et 50 maximum !!")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, max=50, minMessage="8 caractères minimum et 50 maximum !!")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, max=50, minMessage="8 caractères minimum et 50 maximum !!")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="confirm_password", message="Les password ne sont pas identiques")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Note", mappedBy="user", orphanRemoval=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rib;

    /**
     * @ORM\Column(type="date")
     */
    private $embauche;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="author", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Message", mappedBy="recipient")
     */
    private $receivedMessages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="auteur", orphanRemoval=true)
     */
    private $messagesSend;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="destinataire", orphanRemoval=true)
     */
    private $messagesReceived;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->messagesSend = new ArrayCollection();
        $this->messagesReceived = new ArrayCollection();
    }

    public function getId() : ? int
    {
        return $this->id;
    }

    public function getName() : ? string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail() : ? string
    {
        return $this->email;
    }

    public function setEmail(string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername() : ? string
    {
        return $this->username;
    }

    public function setUsername(string $username) : self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword() : ? string
    {
        return $this->password;
    }

    public function setPassword(string $password) : self
    {
        $this->password = $password;

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

    /**
     * @return Collection|Note[]
     */
    public function getNotes() : Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note) : self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Note $note) : self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getUser() === $this) {
                $note->setUser(null);
            }
        }

        return $this;
    }

    public function eraseCredentials()
    {

    }

    public function getSalt()
    {

    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles) : self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPhone() : ? string
    {
        return $this->phone;
    }

    public function setPhone(string $phone) : self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getRib() : ? string
    {
        return $this->rib;
    }

    public function setRib(string $rib) : self
    {
        $this->rib = $rib;

        return $this;
    }

    public function getEmbauche() : ? \DateTimeInterface
    {
        return $this->embauche;
    }

    public function setEmbauche(\DateTimeInterface $embauche) : self
    {
        $this->embauche = $embauche;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages() : Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message) : self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message) : self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesSend(): Collection
    {
        return $this->messagesSend;
    }

    public function addMessagesSend(Message $messagesSend): self
    {
        if (!$this->messagesSend->contains($messagesSend)) {
            $this->messagesSend[] = $messagesSend;
            $messagesSend->setAuteur($this);
        }

        return $this;
    }

    public function removeMessagesSend(Message $messagesSend): self
    {
        if ($this->messagesSend->contains($messagesSend)) {
            $this->messagesSend->removeElement($messagesSend);
            // set the owning side to null (unless already changed)
            if ($messagesSend->getAuteur() === $this) {
                $messagesSend->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesReceived(): Collection
    {
        return $this->messagesReceived;
    }

    public function addMessagesReceived(Message $messagesReceived): self
    {
        if (!$this->messagesReceived->contains($messagesReceived)) {
            $this->messagesReceived[] = $messagesReceived;
            $messagesReceived->setDestinataire($this);
        }

        return $this;
    }

    public function removeMessagesReceived(Message $messagesReceived): self
    {
        if ($this->messagesReceived->contains($messagesReceived)) {
            $this->messagesReceived->removeElement($messagesReceived);
            // set the owning side to null (unless already changed)
            if ($messagesReceived->getDestinataire() === $this) {
                $messagesReceived->setDestinataire(null);
            }
        }

        return $this;
    }

}
