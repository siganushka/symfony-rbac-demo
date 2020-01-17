<?php

namespace App\Entity;

interface ResourceInterface
{
    public function getId(): ?int;

    public function isNew(): bool;

    public function isEqual(?self $target): bool;
}
