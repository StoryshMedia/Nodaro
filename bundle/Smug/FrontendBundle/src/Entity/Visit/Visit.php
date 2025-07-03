<?php

namespace Smug\FrontendBundle\Entity\Visit;

use Smug\Core\Entity\Base\BaseModel;
use \DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Attribute\Groups;

#[Entity]
#[Table('visit')]
class Visit extends BaseModel
{
    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $slug;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $mode;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $pageType;

    #[Column(type: 'datetime')]
    #[Groups(['public'])]
    protected DateTime $visitDate;
}
