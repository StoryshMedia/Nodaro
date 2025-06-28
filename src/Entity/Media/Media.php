<?php

namespace Smug\Core\Entity\Media;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Service\Base\Components\Provider\DataProvider\PathProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Attribute\Groups;

#[Entity]
#[Table('media')]
class Media extends BaseModel
{
    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $file;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $path;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $type;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $extension;

    #[Column(type: 'integer')]
    #[Groups(['public'])]
    protected int $size;

    #[Column(type: 'integer')]
    #[Groups(['public'])]
    protected int $sizeX;

    #[Column(type: 'integer')]
    #[Groups(['public'])]
    protected int $sizeY;

    #[OneToMany(targetEntity: MediaThumbnail::class, mappedBy: 'media')]
    #[Groups(['public'])]
    protected Collection $thumbnails;

    #[Column(type: 'boolean')]
    #[Groups(['public'])]
    protected bool $optimized = true;

    public function __construct()
    {
        $this->thumbnails = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFullPath(): string
    {
        return PathProvider::getHost() . '/' . $this->__get('path');
    }
}
