<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Converter;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use League\Bundle\OAuth2ServerBundle\Converter\UserConverterInterface;
use League\Bundle\OAuth2ServerBundle\Entity\User as LeagueUser;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserConverter implements UserConverterInterface
{
    public function toLeague(UserInterface $user): UserEntityInterface
    {
        $userEntity = new LeagueUser();
        
        // Verwende die User-ID als Identifier
        if ($user instanceof User) {
            $userEntity->setIdentifier($user->getId()->getValue());
        } else {
            $userEntity->setIdentifier($user->getUserIdentifier());
        }

        return $userEntity;
    }
} 