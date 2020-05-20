<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Cette adresse mail est déjà utilisée."
 * )
 */
class User implements UserInterface
{
    /**
     * @Groups({"user"})
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @Groups({"user"})
     * @Assert\NotBlank
     * @Assert\Email
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(type="string")
     * )
     * @ORM\Column(type="json")
     * @Groups({"user"})
     */
    private $roles = [];

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *     min="8"
     * )
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Token", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $tokens;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $state;

    /**
     * @SWG\Property(type="string")
     */
    private $salt;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=50)
     * @Groups({"user"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="date"),
     * @Assert\NotBlank,
     * @Assert\Date
     * @Groups({"user"})
     */
    private $birthdate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Greenwalk", inversedBy="author")
     * @ORM\JoinColumn(nullable=false)
     */
    private $authorGreenwalk;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Greenwalk", inversedBy="participant")
     */
    private $registeredGreenwalk;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
        $this->setState(true);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Token[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): string
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setUser($this);
        }

        return $token;
    }

    public function removeToken(Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
            // set the owning side to null (unless already changed)
            if ($token->getUser() === $this) {
                $token->setUser(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getAuthorGreenwalk(): ?Greenwalk
    {
        return $this->authorGreenwalk;
    }

    public function setAuthorGreenwalk(?Greenwalk $authorGreenwalk): self
    {
        $this->authorGreenwalk = $authorGreenwalk;

        return $this;
    }

    public function getRegisteredGreenwalk(): ?Greenwalk
    {
        return $this->registeredGreenwalk;
    }

    public function setRegisteredGreenwalk(?Greenwalk $registeredGreenwalk): self
    {
        $this->registeredGreenwalk = $registeredGreenwalk;

        return $this;
    }
}
