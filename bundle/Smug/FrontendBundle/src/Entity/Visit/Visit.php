<?php

namespace Smug\FrontendBundle\Entity\Visit;

use Smug\Core\Entity\Base\BaseModel;
use \DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('visit')]
class Visit extends BaseModel
{
    #[Column(type: 'string')]
    protected string $slug;

    #[Column(type: 'string')]
    protected string $mode;

    #[Column(type: 'string')]
    protected string $pageType;

    #[Column(type: 'datetime')]
    protected DateTime $visitDate;
}
