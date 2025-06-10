<?php

namespace Smug\SearchBundle\Service\Search\Listing;

use Smug\SolrBundle\Factory\SolrClientFactory;
use Smug\Core\Service\Base\Service\ListBaseService;
use Smug\Core\Service\Base\Service\Provider\PreviewImageProvider;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\GenreBundle\Entity\Genre\Genre;

/**
 * Class ListService
 * @package Smug\Core\Service\Frontend\Search\Listing
 */
class ListService extends ListBaseService
{
    /**
     * @inheritDoc
     */
    public function getSearch(string $queryString, int $maxResults = 3): array
    {
        $results[] = $this->getSearchPublications($queryString, $maxResults);
        $results[] = $this->getSearchAuthors($queryString, $maxResults);
        $results[] = $this->getSearchUsers($queryString, $maxResults);
        $results[] = $this->getSearchMarkets($queryString, $maxResults);
        $results[] = $this->getSearchStories($queryString, $maxResults);

        return $results;
    }

    /**
     * @param array $params
     * @param string $mode
     * @return array
     */
    public function getSearchDetail(array $params, string $mode): array
    {
        switch ($mode) {
            case 'authors':
                $queryString = 'label:' . DataHandler::getReplaceString(' ', '\ ', $params['search']);
                $core = 'authors';
                $label = 'label';
                $model = 'authors';
                $fields = ['firstName', 'lastName', 'image', 'additional', 'path', 'slug', 'score', 'label'];
                break;
            case 'publication':
                $queryString = 'title:' . $params['search'] . ' OR isbnLong:' . $params['search'] . ' OR isbn:' . $params['search'];
                $core = 'publications';
                $label = 'title';
                $model = 'publications';
                $fields = ['title', 'subTitle', 'image', 'rating', 'additional', 'path', 'slug', 'score', 'teaser', 'summary', 'label'];
                break;
            case 'story':
                $queryString = 'title:' . $params['search'];
                $core = 'stories';
                $label = 'title';
                $model = 'stories';
                $fields = ['title', 'subTitle', 'image', 'rating', 'path', 'slug', 'score', 'label'];
                break;
        }


        $rangeFrom = $params['page'] * $params['limit'];

        $client = SolrClientFactory::getSolrClient($core);

        $query = $client->createSelect(array(
            'query' => $queryString,
            'fields' => $fields
        ));
        if ($params['page'] > 1) {
            $query->setStart(($params['limit'] * ($params['page'] - 1)) + 1);
        }
        $query->setRows($params['limit']);
        $sortDirection = $query::SORT_DESC;
        $query->addSort('score', $sortDirection);
        $rerank = $query->getReRankQuery();
        $rerank->setQuery($label . ':10');
        $rerank->setWeight(6);
        $resultset = $client->select($query);

        $items = [];
        foreach ($resultset as $document) {
            $subItem = [];
            foreach ($document as $field => $value) {
                if ($field === 'image' || $field === 'additional') {
                    $subItem[$field] = DataHandler::getJsonDecode($value, true);
                } elseif (DataHandler::isArray($value)) {
                    $subItem[$field] = DataHandler::getFirstArrayElement($value);
                } else {
                    $subItem[$field] = $value;
                }
            }
            $items[] = $subItem;
        }

        $arPages = [];
        $count = 1;
        $pages = ceil($resultset->getNumFound() / $params['limit']);

        for ($count; $count <= $pages; $count++) {
            if (
                $count === 1 ||
                ($count - $params['page'] < 3 && $count - $params['page'] >= 0) ||
                ($count - $params['page'] >= -3 && $count - $params['page'] <= 0) ||
                $count === $pages
            ) {
                $arPages[] = $count;
            }
        }

        $rangeFrom = $params['page'] * $params['limit'];

        return [
            $params['mode'] => $mode,
            'page' => $params['page'],
            'limit' => $params['limit'],
            $model => $items,
            'absolute' => $resultset->getNumFound(),
            'range' => [
                'from' => ($params['page'] - 1) * $params['limit'] + 1,
                'to' => ($rangeFrom < $resultset->getNumFound()) ? $rangeFrom : $resultset->getNumFound()
            ],
            'pages' => $arPages,
            'lastPage' => $pages
        ];
    }

    /**
     * @param string $queryString
     * @param int $maxResults
     * @return array
     */
    public function getSearchAuthors(string $queryString, int $maxResults = 12): array
    {
        $items = [];

        $client = SolrClientFactory::getSolrClient('authors');

        $query = $client->createSelect(array(
            'query' => 'label:' . DataHandler::getReplaceString(' ', '\ ', $queryString),
            'fields' => ['firstName', 'lastName', 'image', 'additional', 'path', 'slug', 'score', 'label']
        ));
        $query->addSort('score', $query::SORT_DESC);
        $rerank = $query->getReRankQuery();
        $rerank->setQuery('label:10');
        $rerank->setWeight(6);
        $resultset = $client->select($query);
        
        $items = [];
        foreach ($resultset as $document) {
            $subItem = [];
            foreach ($document as $field => $value) {
                if ($field === 'image' || $field === 'additional') {
                    $subItem[$field] = DataHandler::getJsonDecode($value, true);
                } elseif (DataHandler::isArray($value)) {
                    $subItem[$field] = DataHandler::getFirstArrayElement($value);
                } else {
                    $subItem[$field] = $value;
                }
            }
            $items[] = $subItem;
        }

        return [
            'label' => 'AUTHORS',
            'detailMode' => 'author',
            'results' => $items,
            'marketing' => []
        ];
    }

    /**
     * @param string $queryString
     * @param int $maxResults
     * @return array
     */
    public function getSearchUsers(string $queryString, int $maxResults = 12): array
    {
        $items = [];

        $client = SolrClientFactory::getSolrClient('users');

        $query = $client->createSelect(array(
            'query' => 'label:' . DataHandler::getReplaceString(' ', '\ ', $queryString),
            'fields' => ['firstName', 'lastName', 'image', 'additional', 'path', 'slug', 'score', 'label']
        ));
        $query->addSort('score', $query::SORT_DESC);
        $rerank = $query->getReRankQuery();
        $rerank->setQuery('label:10');
        $rerank->setWeight(6);
        $resultset = $client->select($query);
        
        $items = [];
        foreach ($resultset as $document) {
            $subItem = [];
            foreach ($document as $field => $value) {
                if ($field === 'image' || $field === 'additional') {
                    $subItem[$field] = DataHandler::getJsonDecode($value, true);
                } elseif (DataHandler::isArray($value)) {
                    $subItem[$field] = DataHandler::getFirstArrayElement($value);
                } else {
                    $subItem[$field] = $value;
                }
            }
            $items[] = $subItem;
        }

        return [
            'label' => 'USERS',
            'detailMode' => 'user',
            'results' => $items,
            'marketing' => []
        ];
    }

    /**
     * @param string $queryString
     * @param int $maxResults
     * @return array
     */
    public function getSearchMarkets(string $queryString, int $maxResults = 12): array
    {
        $items = [];

        $client = SolrClientFactory::getSolrClient('markets');

        $query = $client->createSelect(array(
            'query' => 'title:' . DataHandler::getReplaceString(' ', '\ ', $queryString),
            'fields' => ['title', 'image', 'path', 'slug', 'score']
        ));
        $query->addSort('score', $query::SORT_DESC);
        $rerank = $query->getReRankQuery();
        $rerank->setQuery('title:10');
        $rerank->setWeight(6);
        $resultset = $client->select($query);
        
        $items = [];
        foreach ($resultset as $document) {
            $subItem = [];
            foreach ($document as $field => $value) {
                if ($field === 'image' || $field === 'additional') {
                    $subItem[$field] = DataHandler::getJsonDecode($value, true);
                } elseif (DataHandler::isArray($value)) {
                    $subItem[$field] = DataHandler::getFirstArrayElement($value);
                } else {
                    $subItem[$field] = $value;
                }
            }
            $items[] = $subItem;
        }

        return [
            'label' => 'MARKETS',
            'detailMode' => 'market',
            'results' => $items,
            'marketing' => []
        ];
    }

    /**
     * @param string $queryString
     * @param int $maxResults
     * @return array
     */
    public function getSearchPublications(string $queryString, int $maxResults = 12): array
    {
        $client = SolrClientFactory::getSolrClient('publications');

        $query = $client->createSelect(array(
            'query' => 'title:' . $queryString . ' OR isbnLong:' . $queryString . ' OR isbn:' . $queryString,
            'fields' => ['title', 'subTitle', 'image', 'additional', 'path', 'slug', 'score', 'teaser', 'summary', 'label']
        ));
        $query->addSort('score', $query::SORT_DESC);
        $rerank = $query->getReRankQuery();
        $rerank->setQuery('title:10');
        $rerank->setWeight(3);
        $resultset = $client->select($query);

        $items = [];
        foreach ($resultset as $document) {
            $subItem = [];
            foreach ($document as $field => $value) {
                if ($field === 'image' || $field === 'additional') {
                    $subItem[$field] = DataHandler::getJsonDecode($value, true);
                } elseif (DataHandler::isArray($value)) {
                    $subItem[$field] = DataHandler::getFirstArrayElement($value);
                } else {
                    $subItem[$field] = $value;
                }
            }
            $items[] = $subItem;
        }

        return [
            'label' => 'PUBLICATIONS',
            'results' => $items,
            'detailMode' => 'publication',
            'marketing' => []
        ];
    }

    /**
     * @param string $queryString
     * @param int $maxResults
     * @return array
     */
    public function getSearchStories(string $queryString, int $maxResults = 12): array
    {
        $items = [];
        $marketingItems = [];

        $client = SolrClientFactory::getSolrClient('stories');

        $query = $client->createSelect(array(
            'query' => 'title:' . $queryString,
            'fields' => ['title', 'subTitle', 'image', 'path', 'slug', 'score', 'label']
        ));
        $query->addSort('score', $query::SORT_DESC);
        $rerank = $query->getReRankQuery();
        $rerank->setQuery('title:10');
        $rerank->setWeight(3);
        $resultset = $client->select($query);

        $items = [];
        foreach ($resultset as $document) {
            $subItem = [];
            foreach ($document as $field => $value) {
                if ($field === 'image' || $field === 'additional') {
                    $subItem[$field] = DataHandler::getJsonDecode($value, true);
                } elseif (DataHandler::isArray($value)) {
                    $subItem[$field] = DataHandler::getFirstArrayElement($value);
                } else {
                    $subItem[$field] = $value;
                }
            }
            $items[] = $subItem;
        }

        return [
            'label' => 'USER_STORIES',
            'results' => $items,
            'detailMode' => 'story',
            'marketing' => $marketingItems
        ];
    }

    /**
     * @param string $queryString
     * @param int $maxResults
     * @return array
     */
    public function getSearchGenres(string $queryString, int $maxResults): array
    {
        $items = [];
        $marketingItems = [];

        $queryBuilder = $this->em->createQueryBuilder();
        $stories = $queryBuilder
            ->select('s')
            ->from(Genre::class, 's')
            ->where(
                $queryBuilder->expr()->like('s.title', ':searchTerm')
            )
            ->setParameter('searchTerm', '%' . addcslashes($queryString, '%_') . '%');

        if ($maxResults !== 0) {
            $stories->setMaxResults($maxResults);
        }

        $stories = $stories->getQuery()->getResult();

        /** @var Genre $genre */
        foreach ($stories as $genre) {
            $previewImage = $genre->getMainImage();

            $items[] = [
                'id' => $genre->getId(),
                'label' => $genre->__get('title'),
                'name' => $genre->__get('title'),
                'title' => $genre->__get('title'),
                'image' => PreviewImageProvider::provide([
                    'previewImage' => $previewImage,
                    'em' => $this->em
                ]),
                'slug' => $genre->__get('slug')
            ];
        }

        return [
            'label' => 'GENRES',
            'results' => $items,
            'detailMode' => 'genre',
            'marketing' => $marketingItems
        ];
    }
}
