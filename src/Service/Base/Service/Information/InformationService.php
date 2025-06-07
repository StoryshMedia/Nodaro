<?php

namespace Smug\Core\Service\Base\Service\Information;

use Smug\PublicationBundle\Entity\Author\Author;
use Smug\Core\Service\Base\Service\BaseService;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Service\ChatGpt\ChatGptService;

class InformationService extends BaseService
{
    public function author(array $data): array
    {
        /** Author $author */
        $author = $this->em->getRepository(Author::class)->findOneBy(['slug' => $data['slug']]);

        if (DataHandler::isEmpty($author)) {
            return [
                'success' => false
            ];
        }

        $author->setInformation($data['information']);

        $this->em->persist($author);
        $this->em->flush();

        return [
            'success' => true,
            'id' => $author->getId()
        ];
    }

    public function authorGpt(array $data): array
    {
        /** @var Author $author */
        $author = $this->em->getRepository(Author::class)->findOneBy(['slug' => $data['slug']]);

        if (DataHandler::isEmpty($author) || $author->getInformation() !== ''  || $author->getBiography() !== '' ) {
            return [
                'success' => false
            ];
        }

        $information = ChatGptService::createTextRequest('Erzähle mir etwas über den Autor ' . $data['title']);

        $author->setInformation($information);

        $this->em->persist($author);
        $this->em->flush();

        return [
            'success' => true,
            'id' => $author->getId()
        ];
    }
}
