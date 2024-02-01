<?php

namespace App\Entity;

use App\Repository\SearchFiltersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

class SearchFilters
{
    private ?int $id = null;
    private ?object $categories = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategories(): ?object
    {
        return $this->categories;
    }

    public function setCategories(object $categories): static
    {
        $this->categories = $categories;

        return $this;
    }
}
