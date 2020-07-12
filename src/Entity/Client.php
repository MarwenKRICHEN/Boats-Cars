<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     errorPath="email",
 *     message="cette email est déjà utilisée"
 * )
 */
class Client implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=8, nullable=true, unique=true)
     *      @Assert\Length(
     *      min = 8,
     *      max = 8,
     *      exactMessage= "Le numero cin doit être composé 8 chiffres",
     *      allowEmptyString="true"
     *    )
     *      @Assert\Regex(pattern="/\d/",
     *     message="La cin est composée de chiffres seulement")
     */
    private $cin;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="integer")
     *      @Assert\Length(
     *      min = 8,
     *      max = 8,
     *      exactMessage= "Le numero de telephone doit être composé 8 chiffres",
     *    )
     *
     */
    private $telephone;

    /**
     * @Assert\Length( min = 8 , minMessage="Votre mot de passe est trop court")
     * @Assert\Regex(pattern="/.*\d+.*$/",
     *     message="Votre mot de passe doit contenir au moins un chiffre")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var
     * @Assert\EqualTo( propertyPath="password", message="Vous n'avez pas tapé le même mot de passe ")
     */
    private $confirm_password;

    /**
     * @ORM\OneToMany(targetEntity=Bateaux::class, mappedBy="owner", orphanRemoval=true)
     */
    private $bateaux;

    /**
     * @ORM\OneToMany(targetEntity=Moteur::class, mappedBy="owner", orphanRemoval=true)
     */
    private $moteurs;

    /**
     * @ORM\Column(type="boolean", length=255)
     */
    private $role = false ;

    public function __construct()
    {
        $this->bateaux = new ArrayCollection();
        $this->moteurs = new ArrayCollection();
    }

    /**
     * @param bool $role
     */
    public function setRole(bool $roles): void
    {
        $this->role = $roles;
    }

    public function getRole() : ?bool
    {
        return $this->role;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirm_password;
    }

    /**
     * @param mixed $confirm_password
     */
    public function setConfirmPassword($confirm_password): void
    {
        $this->confirm_password = $confirm_password;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Bateaux[]
     */
    public function getBateaux(): Collection
    {
        return $this->bateaux;
    }

    public function addBateaux(Bateaux $bateaux): self
    {
        if (!$this->bateaux->contains($bateaux)) {
            $this->bateaux[] = $bateaux;
            $bateaux->setOwner($this);
        }

        return $this;
    }

    public function removeBateaux(Bateaux $bateaux): self
    {
        if ($this->bateaux->contains($bateaux)) {
            $this->bateaux->removeElement($bateaux);
            // set the owning side to null (unless already changed)
            if ($bateaux->getOwner() === $this) {
                $bateaux->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Moteur[]
     */
    public function getMoteurs(): Collection
    {
        return $this->moteurs;
    }

    public function addMoteur(Moteur $moteur): self
    {
        if (!$this->moteurs->contains($moteur)) {
            $this->moteurs[] = $moteur;
            $moteur->setOwner($this);
        }

        return $this;
    }

    public function removeMoteur(Moteur $moteur): self
    {
        if ($this->moteurs->contains($moteur)) {
            $this->moteurs->removeElement($moteur);
            // set the owning side to null (unless already changed)
            if ($moteur->getOwner() === $this) {
                $moteur->setOwner(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
    public function __toString(): string
    {
        return $this->getCin();
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        if($this->getRole() == false)
            return ['ROLE_USER'];
        else
            return ['ROLE_ADMIN'];
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
