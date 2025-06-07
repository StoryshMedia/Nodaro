<?php

namespace Smug\SystemBundle\Entity\Country;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('country')]
class Country extends BaseModel
{
    #[Column(type: 'string')]
    protected string $title = '';

    #[Column(type: 'string')]
    protected string $token = '';

    #[Column(type: 'boolean')]
    protected bool $defaultCountry = false;
}
