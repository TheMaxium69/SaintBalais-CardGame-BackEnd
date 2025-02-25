<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['card:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['card:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['card:read'])]
    private ?int $type = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['card:read'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['card:read'])]
    private ?int $rarity = null;

    #[ORM\Column]
    #[Groups(['card:read'])]
    private ?int $version = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['card:read'])]
    private ?Picture $card_front = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['card:read'])]
    private ?Picture $card_back = null;

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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getRarity(): ?int
    {
        return $this->rarity;
    }

    public function setRarity(int $rarity): static
    {
        $this->rarity = $rarity;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getCardFront(): ?picture
    {
        return $this->card_front;
    }

    public function setCardFront(?picture $card_front): static
    {
        $this->card_front = $card_front;

        return $this;
    }

    public function getCardBack(): ?Picture
    {
        return $this->card_back;
    }

    public function setCardBack(?Picture $card_back): static
    {
        $this->card_back = $card_back;

        return $this;
    }
}
