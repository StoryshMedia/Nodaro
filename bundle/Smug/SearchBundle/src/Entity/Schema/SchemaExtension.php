<?php

namespace Smug\SearchBundle\Entity\Schema;

use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Smug\Core\Entity\Base\Attribute\ExtensionTarget;
use Smug\Core\Entity\Base\BaseModel;
use Smug\FrontendBundle\Entity\Domain\Domain;
use Smug\SearchBundle\Entity\SearchWindow\SearchWindow;
use Symfony\Component\Serializer\Attribute\Groups;

class SchemaExtension extends BaseModel
{
    #[ExtensionTarget(Domain::class)]
    #[OneToOne(targetEntity: SearchWindow::class, mappedBy: 'domain')]
    #[JoinColumn(name: 'search_window_id', referencedColumnName: 'id', onDelete: 'SET NULL', nullable: true)]
    #[Groups(['minimal'])]
    protected SearchWindow $searchWindow;
}
