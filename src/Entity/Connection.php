<?php

namespace App\Entity;

use App\Repository\ConnectionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

/**
 * Explanation:
 * One Connection record indicates that there is
 * One user and is connected to another user
 * So a user can have one-to-many relation with connection
 *
 * While, connectedUser column is the column which has a value to which a user is connected to .
 * A user can be connected to another and vice versa
 * So when a connection is made , two records are inserted
 * eg
 * 1 A B
 * 2 B A
 * 1 A C
 * 2 C A
 * 3 B C
 * 4 C B
 *
 * To find all connections of A , one can use User->getConnections() and then for individual connection
 * user find relation between A and B
 */
#[ORM\Entity(repositoryClass: ConnectionRepository::class)]
#[ORM\UniqueConstraint(name: "unique_user_connection", columns: ['user_id', 'connected_to_user_id'])]
class Connection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Ignore]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'connections')]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'connections')]
    #[ORM\JoinColumn(nullable: false)]
    #[SerializedName("user")]
    private ?User $connectedToUser = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getConnectedToUser(): ?User
    {
        return $this->connectedToUser;
    }

    public function setConnectedToUser(User $connectedToUser): static
    {
        $this->connectedToUser = $connectedToUser;

        return $this;
    }

}
