<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'unique_email', columns: ['email'])]
#[ORM\UniqueConstraint(name: 'unique_auth_token', columns: ['auth_token'])]
#[ORM\UniqueConstraint(name: 'unique_user_code', columns: ['user_code'])]
#[HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[SerializedName("serverId")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[Ignore]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $password = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $authToken = null;

    #[Ignore]
    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[Ignore]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $updatedAt = null;

    #[Ignore]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $tokenRefreshedAt = null;

    #[ORM\Column(options: ["unsigned" => true])]
    private ?int $userCode = null;

    #[ORM\Column]
    #[Ignore]
    private array $roles = [];

    /**
     * @var Collection<int, Connection>
     */
    #[Ignore]
    #[ORM\OneToMany(targetEntity: Connection::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $connections;


    #[ORM\OneToOne(targetEntity: PersonalData::class, mappedBy: 'user')]
    private PersonalData $personalData;

    public function __construct()
    {
        $this->connections = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }


    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    public function setAuthToken(string $authToken): static
    {
        $this->authToken = $authToken;
        $this->setTokenRefreshedAt(new DateTime());
        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTokenRefreshedAt(): ?DateTimeInterface
    {
        return $this->tokenRefreshedAt;
    }

    public function setTokenRefreshedAt(DateTimeInterface $tokenRefreshedAt): static
    {
        $this->tokenRefreshedAt = $tokenRefreshedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->setUpdatedAtValue($this->createdAt);

    }

    /**
     * @param $date
     * @return void
     */
    #[ORM\PreUpdate]
    public function setUpdatedAtValue($date): void
    {
        $this->updatedAt = $date;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserCode(): ?int
    {
        return $this->userCode;
    }

    public function setUserCode(int $userCode): static
    {
        $this->userCode = $userCode;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, Connection>
     */
    public function getConnections(): Collection
    {
        return $this->connections;
    }

    public function addConnection(Connection $connection): static
    {
        if (!$this->connections->contains($connection)) {
            $this->connections->add($connection);
            $connection->setUser($this);
        }

        return $this;
    }

    public function removeConnection(Connection $connection): static
    {
        if ($this->connections->removeElement($connection)) {
            // set the owning side to null (unless already changed)
            if ($connection->getUser() === $this) {
                $connection->setUser(null);
            }
        }

        return $this;
    }

    public function getPersonalData(): PersonalData
    {
        return $this->personalData;
    }

    public function setPersonalData(PersonalData $personalData): void
    {
        $this->personalData = $personalData;
    }


}
