<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 */
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private string $first_name;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private string $last_name;

    /**
     * @ORM\Column(type="string", length=12, nullable=false)
     */
    private string $phone_number;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private string $country;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $town = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $password;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $sts;

    public function __construct()
    {
        $this->sts = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): static
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): static
    {
        $this->town = $town;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function serialize(): array
    {
        return [
          'id'              => $this->id,
          'first_name'      => $this->first_name,
          'last_name'       => $this->last_name,
          'email'           => $this->email,
          'phone_number'    => $this->phone_number,
          'town'            => $this->town,
          'country'         => $this->country,
        ];
    }

    public function getRoles(): array
    {
        return [
            'ROLE_USER'
        ];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->getId();
    }

    /**
     * @return Collection<int, Post>
     */
    public function getSts(): Collection
    {
        return $this->sts;
    }

    public function addSt(Post $st): static
    {
        if (!$this->sts->contains($st)) {
            $this->sts->add($st);
            $st->setUserId($this);
        }

        return $this;
    }

    public function removeSt(Post $st): static
    {
        if ($this->sts->removeElement($st)) {
            // set the owning side to null (unless already changed)
            if ($st->getUserId() === $this) {
                $st->setUserId(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(): static
    {
        $this->created_at = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(): static
    {
        $this->updated_at = new \DateTimeImmutable();

        return $this;
    }
}
