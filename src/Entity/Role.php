<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\Entity\ResourceInterface;
use Siganushka\GenericBundle\Entity\ResourceTrait;
use Siganushka\GenericBundle\Entity\TimestampableInterface;
use Siganushka\GenericBundle\Entity\TimestampableTrait;
use Siganushka\RBACBundle\Model\RoleableInterface;
use Siganushka\RBACBundle\Model\RoleableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 */
class Role implements ResourceInterface, RoleableInterface, TimestampableInterface
{
    use ResourceTrait;
    use RoleableTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $name;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
