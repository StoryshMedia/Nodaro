<?php

namespace Smug\Core\Context;

use Doctrine\ORM\Query;

final class SearchContext
{
    private $context;

    public function __construct(
        Context $context
    )
    {
        $this->context = $context;
    }

    public function getSearchQuery(): Query
    {
		$query = $this->context->getEntityManager()->createQueryBuilder();

		$result = $query->select('c')
            ->from($this->context->getRepositoryClass('main'), 'c');

        $whereConditions = $this->context->getConfigItem('whereConditions') ?? [];

        foreach ($whereConditions as $index => $whereCondition) {
            if ($index == 0) {
                $result->where(
                    $query->expr()->like(
                        'c.' . $whereCondition,
                        $query->expr()->literal(
                            '%' . $this->context->getRequestData()['queryString'] . '%'
                        )
                    )
                );
                
                continue;
            }
            
            $result->orWhere(
                $query->expr()->like(
                    'c.' . $whereCondition,
                    $query->expr()->literal(
                        '%' . $this->context->getRequestData()['queryString'] . '%'
                    )
                )
            );
        }

        $restrictions = $this->context->getConfigItem('restrictions') ?? [];

        foreach ($restrictions as $restriction) {
            if ($restriction['type'] === 'standard') {
                $result->andWhere(
                    $query->expr()->eq(
                    'c.' . $restriction['field'],
                    $restriction['value']
                    )
                );
            }
    
            if ($restriction['type'] === 'manyToMany') {
                $result->innerJoin('c.' . $restriction['field'], $restriction['field']);
                $result->andWhere(
                    $query->expr()->in($restriction['field'] . '.id', $restriction['value'])
                );
            }
        }

        $result->setMaxResults($this->context->getConfigItem('maxResults') ?? 12);
            

        return $result->getQuery();
    }
}