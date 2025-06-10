<?php

namespace Smug\FrontendBundle\Service\Factory;

use Doctrine\DBAL\ParameterType;
use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;

class ContentItemPositionFactory
{
    public static function renewPositions(Context $context, array $data, string $baseParentIdentifier): void
    {
        $queryBuilder = $context->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('c')
            ->from(EntityGenerator::getGeneratedEntity(ContentItem::class), 'c')
            ->where('c.' . $baseParentIdentifier . ' = :itemId');

        if (!DataHandler::isEmpty($data['parentId'])) {
            $queryBuilder
                ->andWhere('c.parentId = :parentId')
                ->andWhere('c.rowColumn = :rowColumn')
                ->setParameter('parentId', $data['parentId'], ParameterType::STRING)
                ->setParameter('rowColumn', $data['rowColumn'], ParameterType::INTEGER);    
        }
        
        $queryBuilder
            ->setParameter('itemId', $data[$baseParentIdentifier]['id'], ParameterType::STRING)
			->orderBy('c.position', 'ASC');

        $items = $queryBuilder->getQuery()->getResult();

        $counter = 0;
        foreach ($items as $item) {
            $item->__set('position', $counter);

            $context->getEntityManager()->persist($item);
            $context->getEntityManager()->flush();

            $counter++;
        }
    }
}