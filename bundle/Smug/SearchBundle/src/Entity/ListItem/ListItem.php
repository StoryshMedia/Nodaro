<?php

namespace Smug\SearchBundle\Entity\ListItem;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\Common\Collections\ArrayCollection;
use Smug\SearchBundle\Entity\SearchWindow\SearchWindow;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[Table('search_window_list_item')]
class ListItem extends BaseModel
{
    #[Column(type: 'htmlField')]
    protected string $itemData;

    #[Column(type: 'string')]
    protected string $detailLink;

    #[ManyToOne(targetEntity: SearchWindow::class, inversedBy: 'listItems')]
    #[JoinColumn(name: 'search_window_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected ?SearchWindow $searchWindow;

    #[Column(type: 'boolean')]
    protected bool $hidden = false;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }
}
