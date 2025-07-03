<?php

namespace Smug\SystemBundle\Entity\Country;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Attribute\Groups;

#[Entity]
#[Table('country')]
class Country extends BaseModel
{
    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $title = '';

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $token = '';

    #[Column(type: 'boolean')]
    #[Groups(['public'])]
    protected bool $defaultCountry = false;
}
