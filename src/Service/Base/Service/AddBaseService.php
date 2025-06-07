<?php

namespace Smug\Core\Service\Base\Service;

use Doctrine\ORM\Mapping\ManyToOne;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\PathProvider;
use Smug\Core\Service\Base\Interfaces\AddServiceInterface;
use \Exception;
use ReflectionClass;
use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\AdministrationBundle\Trait\DispatchDataTrait;
use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataPreMappingEvent;
use Smug\Core\Events\Backend\Data\DataPreStepEvent;
use Smug\Core\Hydrator\Base\BaseHydrator;

class AddBaseService extends BaseService implements AddServiceInterface
{
    use DispatchDataTrait;
    
    public function processAdd(
        Context $context,
        bool $import = false
    ) {
        $newModel = $this->addModel($context, $import);

        // set the multiple selection list data if given
        $this->handleSelectionLists($newModel, $context->getConfig(), $context->getRequestData());

        // set the media data (logo, images) if given
        $this->handleMedia($newModel, $context->getConfig(), $context->getRequestData());

        // handle children
        $this->handleChildren($newModel, $context->getConfig(), $context->getRequestData());

        $newModel = $context->getMainRepository()->findOneBy(['id' => $newModel->getId()]);

        $this->em->refresh($newModel);
        
        return $newModel;
    }

    public function addModel(Context $context, bool $import = false): BaseModel {
        $mapValues = [];
		$class = EntityGenerator::getGeneratedEntity($context->getMainRepositoryClass());

        $this->dispatchData($context->getRequestData(), $context, DataPreMappingEvent::class, $class, SystemEvents::DATA_PRE_MAPPING);

        $mapValues = $this->getMapValues($context, $class, $import);

        if (!DataHandler::isEmpty($context->getPreparerInstance())) {
            $context->setRequestData(
                $context->getPreparerInstance()->prepare($context->getRequestData(), $mapValues)
            );
        }

        $data = $context->getRequestData() ?? [];
        $data['user'] = $context->getUser();

        /** @var BaseModel $struct */
        $struct = BaseHydrator::hydrateArray(
            $data,
            $context
        );

        $struct = $this->dispatchData($struct, $context, DataPreStepEvent::class, $class, SystemEvents::DATA_PRE_CREATED);

        $this->em->beginTransaction();
        $this->em->persist($struct);
        $this->em->flush();
        $this->em->commit();

        return $struct;
    }

    private function handleSelectionLists(BaseModel $newModel, array $config, array $data): void
    {
        if (DataHandler::doesKeyExists('selectLists', $config)) {
            $selectLists = $this->getSelectionListValues($data, $config);

            foreach ($config['selectLists'] as $selectList) {
                if (!DataHandler::doesKeyExists('config', $selectList)) {
                    throw new Exception('Required field config cannot be empty.');
                }
                if (!DataHandler::doesKeyExists('identifier', $selectList)) {
                    if (DataHandler::doesKeyExists('nullable', $selectList['config']) && $selectList['config']['nullable'] === true) {
                        continue;
                    }
                    
                    throw new Exception('Required field ' . $selectList['identifier'] . ' cannot be empty.');
                }
                if (!DataHandler::doesKeyExists($selectList['identifier'], $selectLists) || DataHandler::isEmpty($data[$selectList['identifier']])) {
                    if (DataHandler::doesKeyExists('nullable', $selectList['config']) && $selectList['config']['nullable'] === true) {
                        continue;
                    }

                    throw new Exception('Required field ' . $selectList['identifier'] . ' cannot be empty.');
                }

                foreach ($selectLists[$selectList['identifier']] as $selectItem) {
                    $this->processCreateAssociationModel(
                        [
                            'base' => $newModel,
                            'association' => $selectItem,
                            'config' => $selectList['config']
                        ]
                    );
                }
            }
        }
    }

    public function handleChildren(BaseModel $newModel, array $config, array $data) {
        if (!DataHandler::doesKeyExists('children', $config)) {
            return;
        }

        foreach ($config['children'] as $childData) {
            if (!DataHandler::doesKeyExists($childData['identifier'], $data)) {
                continue;
            }

            $childConfig = $childData['config'];

            if (DataHandler::doesKeyExists('multiple', $childConfig) && $childConfig['multiple'] === true) {
                foreach ($data[$childData['identifier']] as $childItem) {
                    $childItem[$childConfig['parentIdentifier']] = $newModel->toArray();

                    $this->addModel($childItem, $childConfig);
                }
            }
        }
    }

    private function handleMedia(BaseModel $newModel, array $config, array $data): void
    {
        if (DataHandler::doesKeyExists('media', $config)) {
            $mediaRepository = $this->em->getRepository(Media::class);
            if (
                DataHandler::doesKeyExists('attachments', $data) ||
                DataHandler::doesKeyExists('images', $data)
            ) {
                $key = (DataHandler::doesKeyExists('attachments', $data)) ? 'attachments' : 'images';

                if (DataHandler::isArray($data[$key])) {
                    $associationClass = $config['media']['class'];

                    foreach ($data[$key] as $image) {
                        if (DataHandler::isArray($image) && !DataHandler::doesKeyExists('id', $image)) {
                            $image = $image['src'];
                        }

                        if (DataHandler::isArray($image) && DataHandler::doesKeyExists('id', $image)) {
                            /** @var Media $imageObj */
                            $imageObj = $mediaRepository->findOneBy(
                                [
                                    'id' => $image['id']
                                ]
                            );
                        } else {
                            $image = DataHandler::getReplaceString(PathProvider::getHost(), '', $image);
                            if (DataHandler::getSubString($image, 0, 1) === '/') {
                                $image = DataHandler::getSubString($image, 1 );
                            }

                            /** @var Media $imageObj */
                            $imageObj = $mediaRepository->findOneBy(
                                [
                                    'path' => $image
                                ]
                            );

                        }

                        if (DataHandler::isEmpty($imageObj)) {
                            continue;
                        }

                        $mediaAssociation = new $associationClass();

                        $mediaAssociation->__set($config['media']['modelIdentifier'], $newModel);
                        $mediaAssociation->__set('media', $imageObj);
                        $mediaAssociation->__set('main', false);

                        $this->em->persist($mediaAssociation);
                        $this->em->flush();
                    }
                }
            }
        }
        if (DataHandler::doesKeyExists('file', $config)) {
            if (
                DataHandler::doesKeyExists('files', $data) &&
                DataHandler::isArray($data['files'])
            ) {
                $associationClass = $config['file']['class'];

                foreach ($data['files'] as $file) {
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

                    $mediaAssociation->__set($config['media']['modelIdentifier'], $newModel);
                    $mediaAssociation->__set('media', $fileObj);
                    $mediaAssociation->__set('main', false);

                    $this->em->persist($mediaAssociation);
                    $this->em->flush();
                }
            }
        }
    }

    /**
     * @param array $data
     * @param array $config
     * @return array
     */
    private function getSelectionListValues(array $data, array $config): array
    {
        $selectLists = [];

        foreach ($config['selectLists'] as $selectList) {
            if (DataHandler::doesKeyExists($selectList['identifier'], $data)) {
                $identifier = (!DataHandler::doesKeyExists('modelIdentifier', $selectList)) ? 'id' : $selectList['modelIdentifier'];
                $selectLists[$selectList['identifier']] = [];
                $selectListRepository = $this->em->getRepository($selectList['class']);

                if (
                    !DataHandler::isArray($data[$selectList['identifier']]) ||
                    DataHandler::isEmpty($data[$selectList['identifier']])
                ) {
                    continue;
                }

                if (!DataHandler::doesKeyExists('id', $data[$selectList['identifier']][0]) && !DataHandler::isEmpty($data[$selectList['identifier']][0])) {
                    foreach ($data[$selectList['identifier']] as $subItem) {
                        $item = $selectListRepository->findOneBy([$identifier => $subItem[$identifier]]);
    
                        if (DataHandler::isEmpty($item)) {
                            continue;
                        }
                        $selectLists[$selectList['identifier']][] = $item;
                    }

                    continue;
                }
                foreach ($data[$selectList['identifier']] as $item) {
                    $item = $selectListRepository->findOneBy([$identifier => $item[$identifier]]);

                    if (DataHandler::isEmpty($item)) {
                        continue;
                    }
                    
                    $selectLists[$selectList['identifier']][] = $item;
                }
            }
        }

        return $selectLists;
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
     * @inheritDoc
     */
    public function add(Context $context, $import = false): array
    {
            $data = $this->processAdd(
                $context,
                $import
            );
        try {
            
            return [
                'success' => true,
                'data' => $data
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }
}
