<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\Command;

class InstallPluginCommand implements Command
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly string $author,
        public readonly ?array $meta = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }
} 