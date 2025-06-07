<?php

namespace Smug\Core\Service\Base\Service;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Handler\PaginationHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileContentProvider;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Interfaces\ListServiceInterface;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Query\QueryService;
use Doctrine\ORM\Query;
use Doctrine\DBAL\ParameterType;
use \Exception;
use Smug\Core\Context\Context;
use Smug\Core\Context\SearchContext;
use Smug\Core\Service\Base\Query\QueryMapper;

class ListBaseService extends BaseService implements ListServiceInterface
{
    public function processGetSingle(Context $context)
    {
        $identifier = 'id';

        if (DataHandler::doesKeyExists('identifier', $context->getConfig())) {
            $identifier = $context->getConfig()['identifier'];
        }

        $builder = $this->em->createQueryBuilder();
        $model = $builder->select('c')
            ->from($context->getMainRepositoryClass(), 'c')
            ->where('c.' . $identifier . ' = :entityId')
            ->setParameter('entityId', $context->getIdentifier(), ParameterType::STRING)
            ->getQuery()->getOneOrNullResult();

        if (DataHandler::isEmpty($model)) {
            return [
                'success' => false,
                'message' => 'No data found for identifier ' . $context->getIdentifier()
            ];
        }

        $disAllowedFields = $context->getSpecialFields(
            DataHandler::getLastArrayElement(DataHandler::explodeArray('\\', $context->getMainRepositoryClass())),
            'hiddenFields'
        );

        if (!DataHandler::doesKeyExists('returnModel', $context->getConfig())) {
            return $model->toArray($disAllowedFields);
        }

        if ($context->getConfig()['returnModel'] === false) {
            return $model->toArray($disAllowedFields);
        }

        return $model;
    }

    public function getPaginationPages(int $pages, int $page) : array
    {
        $arPages = [
            'start' => 1,
            'preSteps' => [],
            'mainSteps' => [],
            'postSteps' => [],
            'end' => $pages
        ];

        if ($page > 1) {
            if ($page > 4) {
                $arPages['mainSteps'][] = $page - 3;
                $arPages['mainSteps'][] = $page - 2;
                $arPages['mainSteps'][] = $page - 1;
                $arPages['mainSteps'][] = $page;
            } else {
                if ($page === 4) {
                    $arPages['mainSteps'][] = 2;
                    $arPages['mainSteps'][] = 3;
                    $arPages['mainSteps'][] = $page;
                }
                if ($page === 3) {
                    $arPages['mainSteps'][] = 2;
                    $arPages['mainSteps'][] = $page;
                }
                if ($page === 2) {
                    $arPages['mainSteps'][] = $page;
                }
            }
        }

        if (($pages - $page) > 3) {
            $arPages['mainSteps'][] = $page + 1;
            $arPages['mainSteps'][] = $page + 2;
            $arPages['mainSteps'][] = $page + 3;
        } else {
            if (($pages - $page) === 3) {
                $arPages['mainSteps'][] = $page + 1;
                $arPages['mainSteps'][] = $page + 2;
            }
            if (($pages - $page) === 2) {
                $arPages['mainSteps'][] = $page + 1;
            }
        }

        if ($arPages['end'] === 1) {
            $arPages['end'] = '';
        }

        if (DataHandler::isEmpty($arPages['mainSteps'])) {
            return $arPages;
        }

        if ($arPages['mainSteps'][0] > 4) {
            $gap = $arPages['mainSteps'][0] - 1;
            $steps = $this->getPaginationDivider($gap);

            $count = $steps;
            for ($count; $count >= 1; $count--) {
                $arPages['preSteps'][] = intdiv($gap, $count);
            }
        }

        if (($pages - DataHandler::getLastArrayElement($arPages['mainSteps'])) > 4) {
            $gap = $pages - DataHandler::getLastArrayElement($arPages['mainSteps']);
            $steps = $this->getPaginationDivider($gap);

            $count = $steps;
            for ($count; $count >= 1; $count--) {
                $arPages['postSteps'][] = $page + intdiv($gap, $count);
            }
        }

        if ($arPages['end'] === 1) {
            $arPages['end'] = '';
        }

        return $arPages;
    }

    public function processGetSearchResultDetails(array $config): array
    {
        $result = [];

        $model = $this->em->getRepository($config['class'])->findOneBy(['id' => $config['id']]);

        if (DataHandler::doesKeyExists('details', $config)) {
            foreach ($config['details'] as $detail) {
                $getFunction = $detail['getFunction'];
                $subResult = [
                    'headline' => $detail['headline']
                ];

                if (DataHandler::doesKeyExists('multiple', $detail) && $detail['multiple'] === true) {
                    $subResult['items'] = ArrayProvider::getObjectsAsArray($model->$getFunction());
                } else {
                    $subResult['items'] = $model->$getFunction();
                }

                $result[] = $subResult;
            }
        }

        if (DataHandler::doesKeyExists('complete', $config) && $config['complete'] === true) {
            return $model->toArray();
        }

        return $result;
    }

    public function processGetSubData(array $config)
    {
        $builder = $this->em->createQueryBuilder();
        $model = $builder->select('c')
            ->from($config['class'], 'c')
            ->where('c.' . $config['identifier'] . ' = :entityId')
            ->setParameter('entityId', $config['id'], ParameterType::STRING)
            ->getQuery()->getOneOrNullResult();

        if (DataHandler::isEmpty($model)) {
            return [
                'success' => false,
                'message' => 'No data found for identifier ' . $config['id']
            ];
        }

        if (!DataHandler::doesPropertyExist($model, $config['field'])) {
            return [
                'success' => false,
                'message' => 'Property "' . $config['field'] . '" not found in Class "' . $config['class'] . '"'
            ];
        }

        if (DataHandler::doesKeyExists('plain', $config) && $config['plain'] === true) {
            return $model->__get($config['field']);
        }
        
        if (DataHandler::doesKeyExists('fields', $config) && DataHandler::isArray($config['fields'])) {
            $items = ArrayProvider::getObjectsFieldsAsArray(
                $model->__get($config['field']),
                $config['fields']
            );
        } else {
            $items = ArrayProvider::getObjectsAsArray($model->__get($config['field']));
        }

        if (DataHandler::doesKeyExists('nested', $config) && $config['nested'] === true) {
            if (DataHandler::isEmpty($items)) {
                return [];
            }

            if (!DataHandler::doesKeyExists('parentId', DataHandler::getFirstArrayElement($items))) {
                return $items;
            }

            return DataHandler::getTree($items);
        } else {
            return $items;
        }
    }

    public function processGetSearch(Context $context): array
    {
        $searchContext = new SearchContext($context);

        return ArrayProvider::getObjectsAsArray($searchContext->getSearchQuery()->getResult());
    }

    public function getItemImages(array $itemImages): array
    {
        $mediaRepo = $this->em->getRepository(Media::class);

        foreach ($itemImages as $imageKey => $image) {
            /** @var Media $media */
            $media = $mediaRepo->findOneBy(['id' => $image['media']['id']]);

            $thumbnails = ArrayProvider::getObjectsAsArray($media->__get('thumbnails'));

            $viewportThumbnails = [];

            foreach ($thumbnails as $thumbnail) {
                $thumbnail = DataHandler::unsetArrayElement($thumbnail, 'id');
                $viewportThumbnails[$thumbnail['viewport']][$thumbnail['variant']] = $thumbnail;
            }

            $image['media']['thumbnails'] = $viewportThumbnails;
            $image = DataHandler::unsetArrayElement($image, 'id');

            $itemImages[$imageKey] = $image;
        }

        if (DataHandler::getArrayLength($itemImages) === 0) {
            $randomFallbackNumber = DataHandler::getRandomPosition(1, 5);

            /** @var Media $fallbackImage */
            $fallbackImage = $mediaRepo->findOneBy(['file' => 'fallback_0' . $randomFallbackNumber]);
            $arFallbackImage = $fallbackImage->toArray();
            $arFallbackImage = DataHandler::unsetArrayElement($arFallbackImage, 'id');
            $arPublicationImage = ['media' => $arFallbackImage];
            $arPublicationImage['media'] = DataHandler::unsetArrayElement($arPublicationImage['media'], 'id');
            $thumbnails = ArrayProvider::getObjectsAsArray($fallbackImage->__get('thumbnails'));
            $viewportThumbnails = [];

            foreach ($thumbnails as $thumbnail) {
                $thumbnail = DataHandler::unsetArrayElement($thumbnail, 'id');
                $viewportThumbnails[$thumbnail['viewport']][$thumbnail['variant']] = $thumbnail;
            }

            $arPublicationImage['media']['thumbnails'] = $viewportThumbnails;
            $itemImages[] = $arPublicationImage;
        }

        return $itemImages;
    }

    public function processGetPaginated(array $config): array
    {
        return PaginationHandler::getPaginatedList($this->getPaginationData($config), $config['params'], $config['model']);
    }

    public function processGetPaginatedQuery(array $config): Query
    {
        return $this->getPaginationData($config);
    }

    public function processGetFields(array $config): array
    {
        return $this->getEntityFields($config['class'], $config['names']);
    }

    public function processGetData(Context $context): array
    {
        return ArrayProvider::getObjectsAsArray($context->getMainRepository()->findAll());
    }

    public function processGetDataFromFile(array $config): array
    {
        if (DataHandler::doesKeyExists('folder', $config)) {
            $data = FileContentProvider::getFilesInFolder($config['file'], $config['folder']);
        } else {
            $data = FileContentProvider::getSystemFileContent($config['file']);
        }

        if (DataHandler::doesKeyExists('setKeyAsId', $config) && $config['setKeyAsId'] === true) {
            foreach ($data as $key => $item) {
                $data[$key]['id'] = $key;
            }
        }

        return $data;
    }

    private function getPaginationData(array $config): Query|array
    {
        $searchParamName = 'name';
        $advancedFilterSettings = [];

        try {
            $preparer = ServiceGenerationFactory::createInstance(QueryMapper::class);
            /** @var QueryService $queryService */
            $queryService = ServiceGenerationFactory::createInstance(QueryService::class, $this->em);
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        $additional = [];
        $joins = [];

        if (DataHandler::doesKeyExists('titleIdentifier', $config)) {
            $searchParamName = $config['titleIdentifier'];
        }
        if (DataHandler::doesKeyExists('selectFields', $config)) {
            $additional['selectFields'] = $config['selectFields'];
        }

        if (DataHandler::doesKeyExists('ignoreHidden', $config['params'])) {
            $additional['ignoreHidden'] = $config['params']['ignoreHidden'];
            unset($config['params']['ignoreHidden']);
        }

        if (DataHandler::doesKeyExists('ignoreUnapproved', $config['params'])) {
            $additional['approved'] = $config['params']['ignoreUnapproved'];
            unset($config['params']['ignoreUnapproved']);
        }

        if (DataHandler::doesKeyExists('ignorePrivateSchools', $config['params'])) {
            $additional['publicListingAllowed'] = $config['params']['ignorePrivateSchools'];
            unset($config['params']['ignorePrivateSchools']);
        }

        if (DataHandler::doesKeyExists('ignorePrivate', $config['params'])) {
            $additional['isPublic'] = $config['params']['ignorePrivate'];
            unset($config['params']['ignorePrivate']);
        }

        if (DataHandler::doesKeyExists('ignoreHiddenInList', $config['params'])) {
            $additional['ignoreHiddenInList'] = $config['params']['ignoreHiddenInList'];
            unset($config['params']['ignoreHiddenInList']);
        }

        if (DataHandler::doesKeyExists('restrict', $config) && DataHandler::isArray($config['restrict'])) {
            $additional = $preparer->getCompanyRestrictionParams(
                $config['restrict']['withJoin'],
                (DataHandler::doesKeyExists('withSubJoin', $config['restrict'])) ? $config['restrict']['withSubJoin'] : ''
            );

            if (DataHandler::doesKeyExists('merge', $config['restrict']) && DataHandler::isArray($config['restrict']['merge'])) {
                $additional = DataHandler::mergeArray($additional, $config['restrict']['merge']);
            }
        }

        if (DataHandler::doesKeyExists('joins', $config) && !DataHandler::isEmpty($config['joins'])) {
            $joins = $config['joins'];
        }

        return $queryService->getFindAllQuery(
            $config['class'],
            $config['fields'],
            $preparer->prepareSearchParams($config['params'], $searchParamName, $advancedFilterSettings),
            $joins,
            $additional
        );
    }

    private function getPaginationDivider(int $size): int
    {
        if ($size > 1000) {
            return 5;
        }

        if ($size > 100) {
            return 3;
        }

        if ($size > 10) {
            return 2;
        }

        return 2;
    }

    public function getSingle(Context $context): array
    {
        return $this->processGetSingle($context);
    }

    public function getData(Context $context): array
    {
        return $this->processGetData(
            $context
        );
    }

    public function getPaginated(Context $context): array
    {
        if (!DataHandler::doesKeyExists('params', $context->getConfig())) {
            $context->setConfigItem('params', $context->getRequestData());
        }

        return $this->processGetPaginated(
            DataHandler::mergeArray([
                    'class' => $context->getRepositories()['main'],
                ],
                $context->getConfig()
            )
        );
    }

    public function getListPreview(Context $context): array
    {
        $items = [];
        $entries = $context->getMainRepository()->findAll();

        foreach ($entries as $entry) {
            $preview = $this->getFeData($entry->getSlug(), $context, $entry);
            
            if (DataHandler::isEmpty($preview)) {
                continue;
            }
            
            $items[] = $preview;
        }

        return $items;
    }

    public function getSubData(Context $context): array
    {
        return $this->processGetSubData([
            'id' => $context->getIdentifier(),
            'identifier' => (!empty($context->getIdentifierKey())) ? $context->getIdentifierKey() : 'id',
            'class' => $context->getMainRepositoryClass(),
            'list' => $context->getConfig()['getListItem'] ?? false,
            'nested' => $context->getConfig()['nested'] ?? true,
            'fields' => $context->getConfig()['fields'] ?? null,
            'field' => $context->getConfig()['field']
        ]);
        try {
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public function getDataByCondition(Context $context): array
    {
        try {
            $identifierKey = $context->getIdentifierKey() ?? 'id';

            $builder = $this->em->createQueryBuilder();
            return ArrayProvider::getObjectsAsArray(
                $builder->select('c')
                ->from($context->getMainRepositoryClass(), 'c')
                ->where('c.' . $identifierKey . ' = :entityId')
                ->setParameter('entityId', $context->getIdentifier(), ParameterType::STRING)
                ->getQuery()->getResult()
            );
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public function getFields($onlyNames = false): array
    {
        return [];
    }

    public function getSearch(string $queryString, int $maxResults = 3): array
    {
        return [];
    }

    public function getSearchResultDetails(string $id): array
    {
        return [];
    }

    /**
     * @param string $name
     * @param BaseModel|null $model
     * @return array
     */
    public function getFeData(string $name, Context $context, BaseModel $model = null): array
    {
        $result = [];
        $additionalData = [];

        if ($model === null) {
            $model = $context->getMainRepository()->findOneBy(['slug' => $name]);
        }

        if ($model === null) {
            $model = $context->getMainRepository()->findOneBy(['identifier' => $name]);
        }

        if (DataHandler::doesMethodExist($model, 'getHidden') && $model->getHidden() === true) {
            return $result;
        }

        if ($model === null) {
            return $result;
        }

        if (DataHandler::doesMethodExist($model, 'getImages')) {
            $additionalData = [
                'images' => $this->getItemImages(ArrayProvider::getObjectsAsArray($model->getImages()))
            ];
        }

        return DataHandler::mergeArray($model->toArray(), $additionalData);
    }
}
