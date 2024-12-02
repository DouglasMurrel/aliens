<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'userOrder', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Groups(["userinfo"])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $medical = null;

    #[Groups(["userinfo"])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $psycological = null;

    #[Groups(["userinfo"])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $food = null;

    #[Groups(["userinfo"])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    #[Groups(["userinfo"])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $additional = null;

    #[Groups(["userinfo"])]
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $school = null;

    /**
     * @var Collection<int, OrderWant>
     */
    #[Groups(["userinfo"])]
    #[ORM\ManyToMany(targetEntity: OrderWant::class, mappedBy: 'userOrder')]
    private Collection $orderWants;

    /**
     * @var Collection<int, OrderCan>
     */
    #[Groups(["userinfo"])]
    #[ORM\ManyToMany(targetEntity: OrderCan::class, mappedBy: 'userOrder')]
    private Collection $orderCans;

    /**
     * @var Collection<int, OrderNoes>
     */
    #[Groups(["userinfo"])]
    #[ORM\ManyToMany(targetEntity: OrderNoes::class, mappedBy: 'userOrder')]
    private Collection $orderNoes;

    #[Groups(["userinfo"])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $contact = null;

    public function __construct()
    {
        $this->orderWants = new ArrayCollection();
        $this->orderCans = new ArrayCollection();
        $this->orderNoes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMedical(): ?string
    {
        return $this->medical;
    }

    public function setMedical(string $medical): static
    {
        $this->medical = $medical;

        return $this;
    }

    public function getPsycological(): ?string
    {
        return $this->psycological;
    }

    public function setPsycological(string $psycological): static
    {
        $this->psycological = $psycological;

        return $this;
    }

    public function getFood(): ?string
    {
        return $this->food;
    }

    public function setFood(string $food): static
    {
        $this->food = $food;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAdditional(): ?string
    {
        return $this->additional;
    }

    public function setAdditional(string $additional): static
    {
        $this->additional = $additional;

        return $this;
    }

    public function getSchool(): ?int
    {
        return $this->school;
    }

    public function setSchool(int $school): static
    {
        $this->school = $school;

        return $this;
    }

    /**
     * @return Collection<int, OrderWant>
     */
    public function getOrderWants(): Collection
    {
        return $this->orderWants;
    }

    public function addOrderWant(OrderWant $orderWant): static
    {
        if (!$this->orderWants->contains($orderWant)) {
            $this->orderWants->add($orderWant);
            $orderWant->addUserOrder($this);
        }

        return $this;
    }

    public function removeOrderWant(OrderWant $orderWant): static
    {
        if ($this->orderWants->removeElement($orderWant)) {
            $orderWant->removeUserOrder($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderCan>
     */
    public function getOrderCans(): Collection
    {
        return $this->orderCans;
    }

    public function addOrderCan(OrderCan $orderCan): static
    {
        if (!$this->orderCans->contains($orderCan)) {
            $this->orderCans->add($orderCan);
            $orderCan->addUserOrder($this);
        }

        return $this;
    }

    public function removeOrderCan(OrderCan $orderCan): static
    {
        if ($this->orderCans->removeElement($orderCan)) {
            $orderCan->removeUserOrder($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderNoes>
     */
    public function getOrderNoes(): Collection
    {
        return $this->orderNoes;
    }

    public function addOrderNo(OrderNoes $orderNo): static
    {
        if (!$this->orderNoes->contains($orderNo)) {
            $this->orderNoes->add($orderNo);
            $orderNo->addUserOrder($this);
        }

        return $this;
    }

    public function removeOrderNo(OrderNoes $orderNo): static
    {
        if ($this->orderNoes->removeElement($orderNo)) {
            $orderNo->removeUserOrder($this);
        }

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }
    
    public function __toString(): string {
        $schoolString = '';
        if ($this->school == 1) $schoolString = 'Новая';
        if ($this->school == 2) $schoolString = 'Старая';
        if ($this->school == 3) $schoolString = 'Неважно';
        return "Email: " . $this->user->getEmail()
                . "\nИмя: " . $this->user->getFullname()
                . "\nСпособы связи: " . $this->contact
                . "\nМедицинские противопоказания: " . $this->medical
                . "\nПсихологические противопоказания: " . $this->psycological
                . "\nПищевые ограничения: " . $this->food
                . "\nШкола: " . $schoolString
                . "\nХочет типажи: " . implode(",\n",$this->orderWants->toArray())
                . "\nНе хочет типажи: " . implode(",\n",$this->orderNoes->toArray())
                . "\nЕще о персонаже: " . $this->comment
                . "\nДополнение: " . $this->additional
                ;
    }
}
