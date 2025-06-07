<?php

namespace Smug\Core\Service\System\Media\Listing;

use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Service\ListBaseService;

class ListService extends ListBaseService
{
    public function proofExistingFile(array $data): ?Media
    {
        /** @var Media $media */
        $media = $this->em->getRepository(Media::class)->findOneBy([
            'file' => $data['file'],
            'extension' => $data['extension']
        ]);

        return $media;
    }
}
