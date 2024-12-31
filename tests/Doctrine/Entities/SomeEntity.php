<?php
namespace Apie\Tests\ApieBundle\Doctrine\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table()]
class SomeEntity
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column()]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}