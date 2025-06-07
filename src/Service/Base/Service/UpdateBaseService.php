<?php

namespace Smug\Core\Service\Base\Service;

use Doctrine\ORM\Mapping\ManyToOne;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Processor\RemoveProcessor;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\PathProvider;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Interfaces\UpdateServiceInterface;
use Doctrine\ORM\NonUniqueResultException;
use \Exception;
use ReflectionClass;
use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Hydrator\Base\BaseHydrator;
use Smug\Core\Service\Base\Components\Helper\Class\ClassHelper;

class UpdateBaseService extends BaseService implements UpdateServiceInterface
{
    /**
     * @param array $data
     * @param bool $import
     * @param array $config
     * @return BaseModel
     */
    public function processUpdate(
        Context $context
    ) {
        $savedModel = $this->saveModel($context);
        $context->setParentModel($savedModel);

        // set the multiple selection list data if given
        $this->handleSelectionLists($context);

        // set the media data (logo, images) if given
        $this->handleMedia($context);

        // handle children
        $this->handleChildren($context);

        // handle all kind of attachments if given
        $this->handleAttachments($context);
        
        $this->em->refresh($savedModel);
        
        return $savedModel;
    }

    public function saveModel (
        Context $context
    ): BaseModel {
		$class = EntityGenerator::getGeneratedEntity($context->getMainRepositoryClass());
        $mapValues = $this->getMapValues($context, $class);

        if (!DataHandler::isEmpty($context->getPreparerInstance())) {
            $context->setRequestData(
                $context->getPreparerInstance()->prepare($context->getRequestData(), $mapValues)
            );
        }

        /** @var BaseModel $struct */
        $struct = BaseHydrator::hydrateArray(
            $context->getRequestData(),
            $context
        );

        $this->em->beginTransaction();
        $this->em->persist($struct);
        $this->em->flush();
        $this->em->commit();

        return $struct;
    }

    public function deteleModel(Context $context, string $id): array
    {
        /** @var BaseModel $model */
        $model = $context->getEntityByIdentifier($id);

        if (DataHandler::isEmpty($model)) {
            return [
                'success' => false,
                'message' => 'No data found for review with identifier ' . $id
            ];
        }

        // if (DataHandler::doesMethodExist($model, 'getAssociationMappings') && $model->getAssociationMappings()) {
        //     foreach ($model->getAssociationMappings() as $mapping) {
        //         if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY) {
        //             if (DataHandler::isEmpty($mapping['joinTable'])) {
        //                 throw new Exception('Parameter "joinTable" has to be given to delete ManyToMany relations');
        //             }
                    
        //             $this->em->getConnection()->executeQuery('delete from ' . $mapping['joinTable']['name'] . ' where ' . $mapping['joinTable']['joinColumns'][0]['name'] . ' = ' . $id);
        //         }
        //         if ($mapping['type'] === ClassMetadataInfo::ONE_TO_MANY) {
        //             $this->em->createQueryBuilder()
        //                 ->delete( $mapping['targetEntity'], 'c')
        //                 ->where('c.' . $mapping['mappedBy'] . ' = :model')
        //                 ->setParameter('model', $id)
        //                 ->getQuery()
        //                 ->execute();

        //         }
        //     }
        // }
        
        $this->em->remove($model);
        $this->em->flush();

        return [
            'success' => true
        ];
    }

    /**
     * @param array $config
     * @return array|null
     */
    public function processRemove(array $config): ?array
    {
        if (!DataHandler::doesKeyExists('multiple', $config)) {
            /** @var BaseModel $model */
            $model = $this->em->getRepository($config['class'])->findOneBy(['id' => $config['id']]);

            if (DataHandler::isEmpty($model)) {
                return null;
            }

            return RemoveProcessor::process($this->em, $this->setter, null, '', [], false, $model);
        }

        foreach ($config['data'] as $item) {
            /** @var BaseModel $model */
            $model = $this->em->getRepository($config['class'])->findOneBy([$config['modelIdentifier'] => $item[$config['modelIdentifier']]]);

            if (DataHandler::isEmpty($model)) {
                continue;
            }

            if (DataHandler::doesKeyExists('children', $config) && DataHandler::isArray($config['children'])) {
                foreach ($config['children'] as $child) {
                    $childRepository = $this->em->getRepository($child['class']);

                    $childObjects = $childRepository->findBy([$child['childIdentifier'] => $model]);

                    /** @var BaseModel $childObject */
                    foreach ($childObjects as $childObject) {
                        return RemoveProcessor::process($this->em, $this->setter, null, '', [], false, $childObject);
                    }
                }
            }

            $delete = RemoveProcessor::process($this->em, $this->setter, null, '', [], false, $model);

            if ($delete['success'] === false) {
                return $delete;
            }
        }

        return [];
    }

    /**
     * @param array $givenImages
     * @param array $compareImages
     * @return array
     */
    public function getCompareMedia(array $givenImages, array $compareImages): array 
    {
        $result = [
            'keep' => [],
            'new' => [],
            'delete' => []
        ];

        $givenImageIds = [];

        foreach ($givenImages as $givenImage) {
            $givenImageIds[] = $givenImage['media']['id'];
        }

        foreach ($compareImages as $compareImage) {
            if (DataHandler::isInArray($compareImage['id'], $givenImageIds)) {
                $result['keep'][] = $compareImage;
                $givenImageIds = DataHandler::unsetArrayElementByValue($givenImageIds, $compareImage['id']);
                continue;
            }
            if (!DataHandler::isInArray($compareImage['id'], $givenImageIds)) {
                $result['new'][] = $compareImage;
                $givenImageIds = DataHandler::unsetArrayElementByValue($givenImageIds, $compareImage['id']);
                continue;
            }
        }

        if (DataHandler::getArrayLength($givenImageIds) > 0) {
            foreach ($givenImages as $givenImage) {
                if (DataHandler::isInArray($givenImage['media']['id'], $givenImageIds)) {
                    $result['delete'][] = $givenImage;
                }
            }
        }

        return $result;
    }

    public function handleChildren(Context $context) {
        if (!DataHandler::doesKeyExists('children', $context->getConfig())) {
            return;
        }

        $childContext = clone($context);
        foreach ($context->getConfig()['children'] as $childData) {
            if (!DataHandler::doesKeyExists($childData['identifier'], $context->getRequestData())) {
                continue;
            }

            $childConfig = $childData['config'];

            if (DataHandler::doesKeyExists('multiple', $childConfig) && $childConfig['multiple'] === true) {
                foreach ($context->getRequestData()[$childData['identifier']] as $childItem) {
                    $childItem[$childConfig['parentIdentifier']] = $context->getParentModel()->toArray();

                    $childContext->buildFromData($childItem, $childConfig['class'] ,$childConfig);
                    
                    $this->saveModel($childContext);
                }
            }
        }
    }

    /**
     * @param BaseModel $savedModel
     * @param array $config
     * @param array $data
     */
    private function handleSelectionLists(Context $context): void
    {
        if (DataHandler::doesKeyExists('selectLists', $context->getConfig()) && DataHandler::isArray($context->getConfig()['selectLists'])) {
            foreach ($context->getConfig()['selectLists'] as $selectList) {
                if (!DataHandler::doesKeyExists($selectList['identifier'], $context->getRequestData())) {
                    continue;
                }
                if (DataHandler::isNull($context->getRequestData()[$selectList['identifier']])) {
                    continue;
                    throw new Exception('Validation failed for field: "' . $selectList['identifier'] . '" and value ""');
                }

                $selections = $this->getEntitiesFromSelectionList(
                    ArrayProvider::getObjectsAsArray($context->getParentModel()->__get($selectList['identifier'])),
                    $context->getRequestData()[$selectList['identifier']]
                );

                foreach ($selections as $key => $values) {
                    foreach ($values as $selection) {
                        if (DataHandler::doesKeyExists('id', $selection)) {
                            /** @var BaseModel $selectionObj */
                            $selectionObj = $this->em->getRepository($selectList['class'])->findOneBy(['id' => $selection['id']]);
                        } else {
                            /** @var BaseModel $selectionObj */
                            $selectionObj = $this->em->getRepository($selectList['class'])->findOneBy(['slug' => $selection['slug']]);
                        }

                        if ($key === 'new') {
                            $this->processCreateAssociationModel(
                                [
                                    'base' => $context->getParentModel(),
                                    'association' => $selectionObj,
                                    'config' => $selectList['config']
                                ]
                            );
                        } else {
                            $this->processRemoveAssociation(
                                [
                                    'base' => $context->getParentModel(),
                                    'association' => $selectionObj,
                                    'config' => $selectList['config']
                                ]
                            );
                            if (DataHandler::doesKeyExists('associationRemoveFunction', $selectList)) {
                                 $this->processRemoveAssociation([
                                    'base' => $context->getParentModel(),
                                    'association' => $selectionObj,
                                    'config' => $selectList['config']
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    private function handleAttachments(Context $context): ?array
    {
        if (DataHandler::doesKeyExists('attachment', $context->getConfig()) && DataHandler::doesKeyExists($context->getConfig()['attachment']['identifier'], $context->getRequestData())) {
            $property = $context->getConfig()['attachment']['property'];

            try {
                /** @var MediaListService $mediaService */
                $mediaService = ServiceGenerationFactory::createInstance(\Smug\Core\Service\System\Media\Listing\ListService::class);
            } catch (Exception $exception) {
                return ExceptionProvider::getException($exception);
            }

            $attachments = $context->getRequestData()[$context->getConfig()['attachment']['identifier']];

            if (DataHandler::isString($attachments)) {
                $attachments = DataHandler::getJsonDecode($attachments, true);
            }

            if (DataHandler::isArray($attachments)) {
                foreach ($attachments as $key => $attachment) {
                    if (DataHandler::doesKeyExists('resetAttachment', $context->getConfig()['attachment']) && $context->getConfig()['attachment']['resetAttachment']) {
                        $attachment = $attachment[0];
                    }

                    /** @var Media $media */
                    $media = $mediaService->proofExistingFile($attachment);

                    if ($media === null) {
                        $media = $attachment;
                    }

                    if ($this->checkExistingAssociation($context->getConfig(), $media, $context->getParentModel()) === true) {
                        continue;
                    }

                    $newAttachment = $this->processCreateMediaAttachment(
                        $media,
                        [
                            $property => $context->getParentModel()
                        ],
                        $context->getConfig()['attachment']['class']
                    );

                    if ($newAttachment['success'] === false) {
                        return $newAttachment;
                    }

                    $returnAttachments[] = $newAttachment['data']->toArray();
                }
            }
        }

        return null;
    }

    /**
     * @param array $config
     * @param Media $media
     * @param BaseModel $model
     * @return array|bool
     */
    private function checkExistingAssociation(array $config, Media $media, BaseModel $model)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        try {
            $association = $queryBuilder
                ->select('ma')
                ->from($config['attachment']['class'], 'ma')
                ->where('ma.media = :media')
                ->andWhere('ma.' . $config['attachment']['property'] . ' = :model')
                ->setParameter('media', $media)
                ->setParameter('model', $model)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $exception) {
            return ExceptionProvider::getException($exception);
        }

        return ($association !== null);
    }

    /**
     * @param Context $context
     */
    private function handleMedia(Context $context): void
    {
        $mediaRepository = $this->em->getRepository(Media::class);

        if (DataHandler::doesKeyExists('images', $context->getRequestData())) {
            if (DataHandler::doesKeyExists('media', $context->getConfig())) {
                $associationClass = $context->getConfig()['media']['class'];

                if (DataHandler::doesKeyExists('removeExisting', $context->getConfig()['media']) && $context->getConfig()['media']['removeExisting'] === true) {
                    $existingMedia = $this->em->getRepository($associationClass)->findBy([$context->getConfig()['media']['modelIdentifier'] => $context->getParentModel()]);

                    /** @var BaseModel $medium */
                    foreach ($existingMedia as $medium) {
                        RemoveProcessor::process($this->em, $this->setter, null, '', [], false, $medium);
                    }
                }

                foreach ($context->getRequestData()['images'] as $image) {
                    if (DataHandler::doesKeyExists(0, $image)) {
                        $image = $image[0];
                    }
                    if (DataHandler::doesKeyExists('id', $image)) {
                        /** @var Media $imageObj */
                        $imageObj = $mediaRepository->findOneBy(
                            [
                                'id' => $image['id']
                            ]
                        );
                    } else {
                        if (
                            !DataHandler::doesKeyExists('src', $image) &&
                            DataHandler::doesKeyExists('media', $image)
                        ) {
                            $image = $image['media'];
                        }
    
                        /** @var Media $imageObj */
                        $imageObj = $mediaRepository->findOneBy(
                            [
                                'id' => $image['id']
                            ]
                        );
                    }

                    if (DataHandler::isEmpty($imageObj)) {
                        continue;
                    }

                    $mediaAssociation = new $associationClass();

                    $mediaAssociation->__set($context->getConfig()['media']['modelIdentifier'], $context->getParentModel());
                    $mediaAssociation->__set('media', $imageObj);
                    $mediaAssociation->__set('main', false);

                    $this->em->persist($mediaAssociation);
                    $this->em->flush();
                }
            }
        }
        if (DataHandler::doesKeyExists('file', $context->getConfig())) {
            if (
                DataHandler::doesKeyExists('files', $context->getRequestData()) &&
                DataHandler::isArray($context->getRequestData()['files'])
            ) {
                $associationClass = $context->getConfig()['file']['class'];
                $setterFunction = $context->getConfig()['file']['function'];

                if (DataHandler::doesKeyExists('removeExisting', $context->getConfig()['file']) && $context->getConfig()['file']['removeExisting'] === true) {
                    $existingMedia = $this->em->getRepository($associationClass)->findBy([$context->getConfig()['file']['modelIdentifier'] => $context->getParentModel()]);

                    /** @var BaseModel $medium */
                    foreach ($existingMedia as $medium) {
                        RemoveProcessor::process($this->em, $this->setter, null, '', [], false, $medium);
                    }
                }

                foreach ($context->getRequestData()['files'] as $file) {
                    if (DataHandler::isArray($file) && !DataHandler::doesKeyExists('id', $file)) {
                        $file = $file['src'];
                    }

                    if (DataHandler::isArray($file) && DataHandler::doesKeyExists('id', $file)) {
                        /** @var Media $fileObj */
                        $fileObj = $mediaRepository->findOneBy(
                            [
                                'id' => $file['id']
                            ]
                        );
                    } else {
                        $file = DataHandler::getReplaceString(PathProvider::getHost(), '', $file);
                        if (DataHandler::getSubString($file, 0, 1) === '/') {
                            $file = DataHandler::getSubString($file, 1 );
                        }

                        /** @var Media $fileObj */
                        $fileObj = $mediaRepository->findOneBy(
                            [
                                'path' => $file
                            ]
                        );
                    }

                    if (DataHandler::isEmpty($fileObj)) {
                        continue;
                    }

                    $mediaAssociation = new $associationClass();

                    $mediaAssociation->__set($context->getConfig()['media']['modelIdentifier'], $context->getParentModel());
                    $mediaAssociation->__set('media', $fileObj);
                    
                    $this->em->persist($mediaAssociation);
                    $this->em->flush();
                }
            }
        }
    }

    /**
     * @param array $data
     * @param array $config
     * @param string $class
     * @param bool $import
     * @return array
     */
    private function getMapValues(Context $context, string $class, bool $import = false): array
    {
        $mapValues = [];

        $rc = new ReflectionClass($class);

        foreach ($rc->getProperties() as $property) {
            $attributes = $property->getAttributes(ManyToOne::class);

            if (DataHandler::isEmpty($attributes)) {
                continue;
            }

            $mapValues[$property->getName()] = $this->mapValue($context->getRequestData(), $attributes[0]->getArguments()['targetEntity'], $import, $property->getName());
        }

        return $mapValues;
    }

    /**
     * @param Context $context
     * @return bool[]
     */
    public function setMain(Context $context): array
    {
        $parent = $context->getConfigItem('modelIdentifier');

        if (DataHandler::isEmpty($parent)) {
            return [
                'success' => false
            ];
        }
        if (!DataHandler::isArray($context->getRequestData()[$parent]) || !DataHandler::doesKeyExists('id', $context->getRequestData()[$parent])) {
            return [
                'success' => false
            ];
        }

        $data = $context->getByIdentifier($context->getRequestData()[$parent]['id'], $context->getConfigItem('modelIdentifier'));

        foreach ($data as $date) {
            $date->setMain(false);
            $this->em->persist($date);
            $this->em->flush();
        }

        $imageObj = $context->getEntityByIdentifier($context->getRequestData()['id']);

        $imageObj->__set('main', true);
        $this->em->persist($imageObj);
        $this->em->flush();

        return [
            'success' => true
        ];
    }

    /**
     * @param Context $context
     * @return []
     */
    public function approve(Context $context, bool $approved = true): array
    {
        $imageObj = $context->getEntityByIdentifier($context->getRequestData()['id']);

        $imageObj->__set('approved',$approved);
        $this->em->persist($imageObj);
        $this->em->flush();

        return [
            'success' => true,
            'data' => $imageObj->toArray()
        ];
    }

    /**
     * @param Context $context
     * @return bool[]
     */
    public function saveImages(Context $context): array
    {
        $repository = $context->getMainRepository();

        foreach ($context->getRequestData()['images'] as $image) {
            $imageObj = $repository->findOneBy(['id' => $image['id']]);

            $imageObj->setMain($image['main']);
            $this->em->persist($imageObj);
        }

        $this->em->flush();

        return [
            'success' => true
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function save(Context $context): array
    {
        $data = $this->processUpdate(
            $context
        );
        
        return [
            'success' => true,
            'data' => $data
        ];
        try {
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(Context $context): array
    {
        try {
            return $this->deteleModel($context, $context->getRequestData()['id']);
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function assign(Context $context): array
    {
        $model = $context->getEntityByIdentifier($context->getRequestData()['model']['id']);

        $rc = new ReflectionClass(get_class($model));

        $property = ClassHelper::getClassProperty($rc, $context->getConfigItem('field'));
        $entityArguments = ClassHelper::getAttributeArguments($property, 'targetEntity');

        $targetEntity = $entityArguments['targetEntity'];
        $targetEntityRc = new ReflectionClass($targetEntity);
        $mappingProperty = ClassHelper::getMappingProperty($targetEntityRc, $context->getConfigItem('field'));
        $mappingTargetEntity = ClassHelper::getAttributeArguments($mappingProperty, 'targetEntity');

        $context->addRepository('target', $mappingTargetEntity['targetEntity']);

        foreach ($context->getRequestData()['item'] as $item) {
            $identifier = $item['id'] ?? $item[$mappingProperty->getName()]['id'];
            $assignModel = new $targetEntity();

            $assignModel->__set($entityArguments['mappedBy'], $model);
            $assignModel->__set($mappingProperty->getName(), $context->getEntityByIdentifier($identifier, 'id', 'target'));

            $context->getEntityManager()->persist($assignModel);
            $context->getEntityManager()->flush();

            $model->__add($context->getConfigItem('field'), $assignModel);
            $context->getEntityManager()->persist($model);
            $context->getEntityManager()->flush();
        }
        
        return [
            'success' => true
        ];
        try {
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function quickEdit(array $data, $import = false): array
    {
        return [];
    }
}
