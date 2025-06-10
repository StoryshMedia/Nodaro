<?php

namespace Smug\FrontendBundle\Service\Search;

use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;

class SearchService
{
    public static function getSearchResults(Context $context): array
    {
        $builder = $context->getEntityManager()->createQueryBuilder();
        $builder->select('c')
          ->from($context->getMainRepositoryClass(), 'c');

        $count = 0;
        
        foreach ($context->getRequestData()['searchFields'] as $searchField) {
            if ($count === 0) {
                $builder->where('c.' . $searchField . ' LIKE :searchTerm');
                continue;
            }
            $builder->orWhere('c.' . $searchField . ' LIKE :searchTerm');
            $count++;
        }
          
        $entries = $builder->setParameter('searchTerm', '%' . $context->getRequestData()['searchTerm'] ?? '' . '%')
          ->getQuery()->getResult();

        if (DataHandler::isEmpty($entries)) {
            return [];
        }

        return ArrayProvider::getObjectsAsArray($entries);
    }
}
