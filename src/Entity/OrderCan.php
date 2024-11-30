<?php

namespace App\Entity;

use App\Repository\OrderCanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderCanRepository::class)]
class OrderCan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["userinfo"])]
    private ?int $id = null;

    #[Groups(["userinfo"])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\ManyToMany(targetEntity: Order::class, inversedBy: 'orderCans')]
    private Collection $userOrder;

    public function __construct()
    {
        $this->userOrder = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getUserOrder(): Collection
    {
        return $this->userOrder;
    }

    public function addUserOrder(Order $userOrder): static
    {
        if (!$this->userOrder->contains($userOrder)) {
            $this->userOrder->add($userOrder);
        }

        return $this;
    }

    public function removeUserOrder(Order $userOrder): static
    {
        $this->userOrder->removeElement($userOrder);

        return $this;
    }
}
