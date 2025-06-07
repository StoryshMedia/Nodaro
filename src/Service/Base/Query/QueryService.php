<?php

namespace Smug\Core\Service\Base\Query;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use \Exception;

/**
 * Class QueryService
 * @package Smug\Core\Service\Base\Query
 */
class QueryService
{
    /** @var EntityManagerInterface $em */
    protected EntityManagerInterface $em;

    /**
     * QueryService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $class
     * @param $selector
     * @param $identifier
     * @return Query
     */
    public function getAssociationQuery($class, $selector, $identifier): Query
    {
        $queryBuilder = $this->em->createQueryBuilder();
        return $queryBuilder->select('ac')
            ->from($class, 'ac')
            ->where('ac.' . $selector . ' = :param')
            ->setParameter('param', $identifier)
            ->getQuery();
    }

    /**
     * @example Contact::class,
     *          [
     *              'searchfield',
     *              'joinSelector.searchfield',
     *              'searchfield',
     *              'searchfield'
     *          ],
     *          $this->contactPreparer->prepareSearchParams($params),
     *          [
     *              'joinSelector'
     *          ]
     *
     * @param string $class
     * @param array $fields
     * @param array $search
     * @param array $joins
     * @param array $additional
     * @return Query|array
     */
    public function getFindAllQuery(
    	string $class,
	    array $fields,
	    array $search,
	    array $joins = [],
	    array $additional = []
    ) {
        $isManyToMany = false;
        $canBeNull = false;
        $canBeEmpty = false;
        $groupBy = [];
    	$queryBuilder = $this->em->createQueryBuilder();

        if (DataHandler::doesKeyExists('selectFields', $additional)) {
            $query = $queryBuilder
            ->select($additional['selectFields'])
            ->from($class, 'c')
            ->distinct();
        } else {
            $query = $queryBuilder
            ->select('c')
            ->from($class, 'c');
        }

	    if (DataHandler::doesKeyExists('fieldDefinitions', $additional)) {
		    $query->addSelect($additional['fieldDefinitions']);
		
		    $additional = DataHandler::unsetArrayElement($additional, 'fieldDefinitions');
	    }
	    
        if (!DataHandler::doesKeyExists('search', $search)) {
            $search['search'] = '';
        }

        if (DataHandler::getArrayLength($joins) > 0) {
            foreach ($joins as $join) {
            	if (DataHandler::isStringInString($join, 'SUBJOIN:')) {
		            $join = DataHandler::getReplaceString('SUBJOIN:', '', $join);
		
		            $subJoins = DataHandler::explodeArray('|', $join);

		            foreach ($subJoins as $subJoin) {
			            if (DataHandler::isStringInString($subJoin, 'MTM_SIMPLE:')) {
                            $subJoin = DataHandler::getReplaceString('MTM_SIMPLE:', '', $subJoin);
				
				            $query->innerJoin('c.' . $subJoin, $subJoin);
				
				            $isManyToMany = true;
			            } else {
			                if (DataHandler::isStringInString($subJoin, '.')) {
                                $joinCondition = DataHandler::explodeArray('.', $subJoin);

                                $query->leftJoin($joinCondition[0] . '.' . $joinCondition[1], $joinCondition[1]);
                            } else {
			                    // TODO check a better solution
                                // set it to get for example all contact with given customerIds AND those who are not connected to any customer
			                    $canBeNull = $subJoin;
                                $query->leftjoin('c.' . $subJoin, $subJoin);
                            }
			            }
		            }
	            } elseif (DataHandler::isStringInString($join, 'MTM:')) {
                    $join = DataHandler::getReplaceString('MTM:', '', $join);
                    $join = DataHandler::explodeArray('.', $join);

                    $query->innerJoin('c.' . $join[0], 's', 'WITH', 's.' . $join[1] . '= :paramMTM');

                    $isManyToMany = true;
                } elseif (DataHandler::isStringInString($join, 'MTM_SIMPLE:')) {
	                $join = DataHandler::getReplaceString('MTM_SIMPLE:', '', $join);
	
	                $query->innerJoin('c.' . $join, $join);

	                $isManyToMany = true;
                    $canBeEmpty = $join;
                }  elseif (DataHandler::isStringInString($join, 'SIMPLE_SUBJOINS:')) {
                    $join = DataHandler::getReplaceString('SIMPLE_SUBJOINS:', '', $join);

                    $values = DataHandler::getJsonDecode($join, true);

                    $query->innerJoin(
                        'c.' . $values['classIdentifier'],
                        $values['classIdentifier']
                    );

                    $query->innerJoin(
                        $values['classIdentifier'] . '.' .  $values['subClassIdentifier'],
                        $values['subClassIdentifier']
                    );
                } elseif (DataHandler::isStringInString($join, 'MTM_SUBJOINS:')) {
	                $join = DataHandler::getReplaceString('MTM_SUBJOINS:', '', $join);

	                $values = DataHandler::getJsonDecode($join, true);
		
		            $query->innerJoin(
			            'c.' . $values['parentIdentifier'],
			            $values['parentIdentifier']
		            );

	                $query->innerJoin(
                        $values['parentIdentifier'] . '.' .  $values['joinIdentifier'],
                        $values['joinIdentifier']
                    );

	                $query->innerJoin(
                        $values['joinIdentifier'] . '.' .  $values['whereColumn'],
                        $values['whereColumn']
                    );

	                $isManyToMany = true;
                } else {
                    $query->leftjoin('c.' . $join, $join);
                }
            }
        }
	
	    if (DataHandler::doesKeyExists('fieldDefinitions', $additional)) {
		    $query->addSelect($additional['fieldDefinitions']);
		
		    $additional = DataHandler::unsetArrayElement($additional, 'fieldDefinitions');
	    }

	    if (DataHandler::doesKeyExists('ignoreHidden', $additional) && $additional['ignoreHidden'] === true) {
		    $query->andWhere('c.hidden = false');
	    }

	    if (DataHandler::doesKeyExists('ignoreHiddenInList', $additional) && $additional['ignoreHiddenInList'] === true) {
		    $query->andWhere('c.hiddenInList = false');
	    }

	    if (DataHandler::doesKeyExists('publicListingAllowed', $additional) && $additional['publicListingAllowed'] === false) {
		    $query->andWhere('c.publicListingAllowed = true');
	    }

	    if (DataHandler::doesKeyExists('isPublic', $additional) && $additional['isPublic'] === true) {
		    $query->andWhere('c.isPublic = true');
	    }

	    if (DataHandler::doesKeyExists('approved', $additional)) {
		    $query->andWhere('c.approved = ' . !$additional['approved']);
	    }

	    if (DataHandler::doesKeyExists('groupBy', $additional)) {
            foreach ($additional['groupBy'] as $groupByItem) {
                $groupBy[] = $groupByItem;
	        }

		    $additional = DataHandler::unsetArrayElement($additional, 'groupBy');
	    }
	    
        if ($search['search'] !== '') {
            $query = $this->getQuerySearch($query, $search['search'], $fields);
        }

        if (DataHandler::isArray($search['filter'])) {
            if (!DataHandler::doesKeyExists('filterData', $search['filter'])) {
                foreach ($search['filter'] as $filterItem) {
                    if (
                        DataHandler::getStringOccurrencesInString($filterItem['filterData']['expression'], ':') ===
                        DataHandler::getArrayLength($filterItem['filterData']['parameters']) &&
                        !DataHandler::isStringInString($filterItem['filterData']['expression'], 'inArray')
                    ) {
                        if (!$isManyToMany) {
                            $query->andWhere(
                                $filterItem['filterData']['expression']
                            );
                        }
        
                        if (DataHandler::doesKeyExists('ignoreManyToMany', $filterItem['filterData'])) {
                            if ($filterItem['filterData']['ignoreManyToMany'] === true && $isManyToMany) {
                                $query->andWhere(
                                    $filterItem['filterData']['expression']
                                );
                            }
                        }
        
                        foreach ($filterItem['filterData']['parameters'] as $parameter) {
                            $query->setParameter($parameter['key'], $parameter['value']);
                        }
                    } elseif (DataHandler::isStringInString($filterItem['filterData']['expression'], 'inArray')) {
                        $keyParts = DataHandler::explodeArray(':', $filterItem['filterData']['expression']);
                        $query->andWhere($queryBuilder->expr()->in($keyParts[1], $filterItem['filterData']['parameters'][0]['value']));
                    } else {
                        if (DataHandler::doesKeyExists('emptyExpression', $filterItem['filterData'])) {
                            $query->andWhere(
                                $filterItem['filterData']['emptyExpression']
                            );
                        } else {
                            $query->andWhere(
                                'c.' . $filterItem['filterData']['parameters'][0]['key'] . ' = ' . $filterItem['filterData']['parameters'][0]['value']
                            );
                        }
                    }
                }
            } else {
                if (
                    DataHandler::getStringOccurrencesInString($search['filter']['filterData']['expression'], ':') ===
                    DataHandler::getArrayLength($search['filter']['filterData']['parameters']) &&
                    !DataHandler::isStringInString($search['filter']['filterData']['expression'], 'inArray')
                ) {
                    if (!$isManyToMany) {
                        $query->andWhere(
                            $search['filter']['filterData']['expression']
                        );
                    }
    
                    if (DataHandler::doesKeyExists('ignoreManyToMany', $search['filter']['filterData'])) {
                        if ($search['filter']['filterData']['ignoreManyToMany'] === true && $isManyToMany) {
                            $query->andWhere(
                                $search['filter']['filterData']['expression']
                            );
                        }
                    }
    
                    foreach ($search['filter']['filterData']['parameters'] as $parameter) {
                        $query->setParameter($parameter['key'], $parameter['value']);
                    }
                } elseif (DataHandler::isStringInString($search['filter']['filterData']['expression'], 'inArray')) {
                    $keyParts = DataHandler::explodeArray(':', $search['filter']['filterData']['expression']);
                    $query->andWhere($queryBuilder->expr()->in($keyParts[1], $search['filter']['filterData']['parameters'][0]['value']));
                } else {
                    if (DataHandler::doesKeyExists('emptyExpression', $search['filter']['filterData'])) {
                        $query->andWhere(
                            $search['filter']['filterData']['emptyExpression']
                        );
                    } else {
                        $query->andWhere(
                            'c.' . $search['filter']['filterData']['parameters'][0]['key'] . ' = ' . $search['filter']['filterData']['parameters'][0]['value']
                        );
                    }
                }
            }
        }

        if (DataHandler::isArray($search['alphabetical'])) {
//            if (!$isManyToMany) {
                $query->andWhere(
                    $search['alphabetical']['expression']
                );
//            }

            $query->setParameter('alphabetical', $search['alphabetical']['value'] . '%');
        }
        
        if (DataHandler::getArrayLength($additional) > 0) {
            foreach ($additional as $add) {
                if (!DataHandler::isArray($add)) {
                    continue;
                }

            	if ($add['expression']['type'] === 'standard') {
		            $query->andWhere(
			            $add['expression']['expression']
		            );

		            foreach ($add['parameters'] as $parameter) {
			            $query->setParameter($parameter['key'], $parameter['value']);
		            }
	            } elseif ($add['expression']['type'] === 'inArray') {
		            foreach ($add['parameters'] as $parameter) {
			            $query->andWhere($queryBuilder->expr()->in($parameter['key'], $parameter['value']));

			            if (!DataHandler::isEmpty($canBeNull)) {
			                $query->orWhere($queryBuilder->expr()->isNull($parameter['key']));
                        }
			            if (!DataHandler::isEmpty($canBeEmpty)) {
			                $query->orWhere('c.' . $canBeEmpty . ' is empty');
                        }
		            }
	            } elseif ($add['expression']['type'] === 'isNull') {
		            $query->andWhere($queryBuilder->expr()->isNull($add['expression']['expression']));
	            }
            }
        }

        if (!DataHandler::isEmpty($groupBy)) {
            $groupByCount = 0;

            foreach ($groupBy as $groupByItem) {
                if ($groupByCount === 0) {
                    $query->groupBy($groupByItem);
                    continue;
                }

                $query->addGroupBy($groupByItem);
            }
        }

        if (DataHandler::isArray($search['sorting']) && DataHandler::doesKeyExists('orderBy', $search['sorting'])) {
            $query->addOrderBy($search['sorting']['orderBy'], $search['sorting']['direction']);
        }


        return $query->getQuery();
    }
	
	/**
	 * @param QueryBuilder $query
	 * @param $search
	 * @param array $fields
	 * @return QueryBuilder
	 */
    protected function getQuerySearch(QueryBuilder $query, $search, array $fields): QueryBuilder
    {
        foreach ($fields as $key => $field) {
            $selector = 'c.' . $field;

            if (DataHandler::isStringInString($field, '.')) {
                $selector = $field;
                $field = DataHandler::getReplaceString('.', '_', $field);
            }

            if ($key === 0) {
                $query->where(
                    $query->expr()->like($selector, ':' . $field)
                );
            } else {
                $query->orWhere(
                    $query->expr()->like($selector, ':' . $field)
                );
            }

            $query->setParameter($field, '%' . addcslashes($search, '%_') . '%');
        }

        return $query;
    }
}
