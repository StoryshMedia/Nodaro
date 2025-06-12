<?php

namespace Smug\Core\Service\Base\Service;

use Smug\Core\DatabaseSetter\Base\BaseSetter;
use Smug\Core\Entity\Base\BaseModel;
use Smug\FrontendUserBundle\Entity\FrontendUser\FrontendUser;
use Smug\SystemBundle\Entity\User\User;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Processor\AssociationModelProcessor;
use Smug\Core\Service\Base\Components\Processor\MediaAttachmentProcessor;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\EventDispatcher\Event;
use \Exception;

class BaseService
{
    /** @var EntityManagerInterface $em */
    protected EntityManagerInterface $em;
    
    /** @var TokenInterface|null $tokenStorage */
    protected ?TokenInterface $tokenStorage;
    
    /** @var EventDispatcherInterface|null $eventDispatcher */
    protected ?EventDispatcherInterface $eventDispatcher = null;
	
	/** @var User $currentUser */
	public ?User $currentUser;

	/** @var FrontendUser $currentFeUser */
	public ?FrontendUser $currentFeUser;

	/** @var ContainerInterface $container */
	public ContainerInterface $container;

	/** @var BaseSetter $setter */
	public BaseSetter $setter;

    public function __construct (EntityManagerInterface $entityManager, KernelInterface $kernel)
    {
        $this->em = $entityManager;
        $this->container = $kernel->getContainer();
        $this->tokenStorage = $kernel->getContainer()->get('security.token_storage')->getToken();
        $this->eventDispatcher = $this->container->get('event_dispatcher');
        if ($this->tokenStorage && !DataHandler::isString($this->tokenStorage->getUser())) {
            /** @var BaseModel $userToken */
            $userToken = $this->tokenStorage->getUser();
            $user = $this->em->getRepository(User::class)->findOneBy(['id' => $userToken->getId()]);
            if (!DataHandler::isEmpty($user)) {
                $this->currentUser = $user;
            } else {
                $this->currentUser = null;
            }
        }
        $this->setter = new BaseSetter($this->em);
    }

    public function dispatch(Event $event, ?string $eventName = null) {
        $this->eventDispatcher->dispatch($event, $eventName);
    }

    public function getUser(): ?User {
        return ($this->currentUser) ? null : $this->currentUser;
    }
	
	public function processRemoveAssociation(
		array $data
	): array
	{
		$this->em->beginTransaction();
		try {
			//remove the association
			$this->setter->removeAssociationModel($data);
			
			$this->em->commit();
			
		} catch (Exception $exception) {
			$this->em->rollback();
			
			return ExceptionProvider::getException($exception);
		}
		
		return [
			'success' => true
		];
	}
	
	public function processCreateMedia(array $data): array
	{
        //set the attachment and assign it to the association given in $class
        $newMedia = $this->setter->setMedia(
            $data
        );
			
		return $newMedia->toArray();
	}
    
    public function processCreateMediaAttachment($attachment, array $data, string $class): array
    {
	    return MediaAttachmentProcessor::process($this->em, $this->setter, $attachment, $data, $class);
    }
	
	public function processCreateAssociationModel(
		array $data
	): array
	{
		return AssociationModelProcessor::process($this->em, $this->setter, $data);
	}
	
	public function handleManyToManyAssociations(BaseModel $baseModel, array $associations, array $config): void
	{
		foreach ($associations as $key => $association) {
			foreach ($association as $singleAssociation) {
				/** @var BaseModel $associationObj */
				$associationObj = $this->em->getRepository($config['associationClass'])->findOneBy(['id' => $singleAssociation['id']]);
				
				if ($key === 'new') {
					$this->processCreateAssociationModel($baseModel->toArray(), $associationObj, $config['addFunction']);
				} else {
					$this->processRemoveAssociation($baseModel->toArray(), $config['addFunction'], $associationObj);
				}
			}
		}
	} 

    public function getEntityFields(string $className, $onlyNames = false): array
    {
        $class = $this->em->getClassMetadata($className);
        $fields = [];
        $result = [];
        $childEntities = [];

        if (!empty($class->discriminatorColumn)) {
            $fields[] = $class->discriminatorColumn['name'];
        }

        $fields = DataHandler::mergeArray($class->getColumnNames(), $fields);

        foreach ($fields as $index => $field) {
            if ($class->isInheritedField($field)) {
                unset($fields[$index]);
            }
        }

        foreach ($class->getAssociationMappings() as $name => $relation) {
            // inverseJoinColumns check
	
            if (!$class->isInheritedAssociation($name)) {
                if (DataHandler::doesKeyExists('joinColumns', $relation)) {
                    foreach ($relation['joinColumns'] as $joinColumn) {
                        $fields[] = [
                        	'name' => DataHandler::getReplaceString('_id', '', $joinColumn['name']),
	                        'targetEntity' => $relation['targetEntity']
                        ];
                    }
                } else {
                    $childEntities[] = [
                        'name' => DataHandler::getCamelCaseString($relation['fieldName']),
                        'targetEntity' => $relation['targetEntity'],
                        'mappedBy' => $relation['mappedBy']
                    ];
                }
            }
        }
	
        if ($onlyNames === true) {
            return $fields;
        }

        foreach ($fields as $field) {
            $result[] = [
                'selected' => false,
                'name' => DataHandler::isArray($field) ? DataHandler::getCamelCaseString($field['name']) : DataHandler::getCamelCaseString($field),
	            'targetEntity' => DataHandler::isArray($field) ? $field['targetEntity'] : null,
	            'mapping' => null
            ];
        }

        foreach ($childEntities as $childEntity) {
            $result[] = [
                'selected' => false,
	            'name' => DataHandler::isArray($childEntity) ? DataHandler::getCamelCaseString($childEntity['name']) : DataHandler::getCamelCaseString($childEntity),
                'targetEntity' => $childEntity['targetEntity'],
                'mapping' => $childEntity['mappedBy']
            ];
        }

        return $result;
    }

    public function mapValue(array $item, string $class, bool $import, string $identifier = ''): ?object
    {
        if (!DataHandler::doesKeyExists($identifier, $item)) {
            return null;
        }

        if (!DataHandler::isArray($item[$identifier])) {
        	if (DataHandler::isEmpty($item[$identifier])) {
		        return null;
	        }
            return $this->em->getRepository($class)->findOneBy([
                $identifier => $item[$identifier]
            ]);
        }

        if ($import) {
	        if (!DataHandler::doesKeyExists('identifier', $item[$identifier])) {
		        return null;
	        }
	
            return $this->em->getRepository($class)->findOneBy([
                $item[$identifier]['identifier'] => $item[$identifier]['value']
            ]);
        } 
        
        if (!DataHandler::doesKeyExists('id', $item[$identifier]) && !DataHandler::doesKeyExists('slug', $item[$identifier])) {
            return null;
        }

        if (DataHandler::doesKeyExists('id', $item[$identifier])) {
            return $this->em->getRepository($class)->find([
                'id' => $item[$identifier]['id']
            ]);
        }

        return $this->em->getRepository($class)->findOneBy([
            'slug' => $item[$identifier]['slug']
        ]);;
    }
	
    public function getEntitiesFromSelectionList(array $existingEntities, array $actualEntities, string $subIdentifier = ''): array
    {
        if (DataHandler::getArrayLength($existingEntities) === 0) {
            return [
                'delete' => [],
                'new' => $actualEntities
            ];
        }

        if (DataHandler::getArrayLength($actualEntities) === 0) {
            return [
                'delete' => $actualEntities,
                'new' => []
            ];
        }

        return [
          'delete' => $this->getCompareItems($existingEntities, $actualEntities, $subIdentifier),
          'new' => $this->getCompareItems($actualEntities, $existingEntities, $subIdentifier)
        ];
    }
	
    private function getCompareItems(array $arEntitiesOne, array $arEntitiesTwo, string $subIdentifier = ''): array
    {
        $result = [];

        if (DataHandler::isEmpty($arEntitiesTwo) || DataHandler::isEmpty($arEntitiesOne)) {
            return $result;
        }

        foreach ($arEntitiesOne as $arEntitiesOneEntity) {
        	if ($arEntitiesOneEntity === null) {
        		continue;
	        }
            if (!DataHandler::isArray($arEntitiesOneEntity)) {
                $arEntitiesOneEntityId = $arEntitiesOneEntity->getId();
            } else {
            	if ($subIdentifier !== '' && DataHandler::doesKeyExists($subIdentifier, $arEntitiesOneEntity)) {
                    $identifierKey = (DataHandler::doesKeyExists('id', $arEntitiesOneEntity[$subIdentifier])) ? 'id' : 'slug';
		            $arEntitiesOneEntityId = $arEntitiesOneEntity[$subIdentifier][$identifierKey];
	            } else {
                    $identifierKey = (DataHandler::doesKeyExists('id', $arEntitiesOneEntity)) ? 'id' : 'slug';
		            $arEntitiesOneEntityId = $arEntitiesOneEntity[$identifierKey];
	            }
            }

            $change = true;
            foreach ($arEntitiesTwo as $arEntitiesTwoEntity) {
            	if ($arEntitiesTwoEntity === null) {
            		continue;
	            }
	            if (!DataHandler::isArray($arEntitiesTwoEntity)) {
		            $arEntitiesTwoEntityId = $arEntitiesTwoEntity->getId();
	            } else {
		            if ($subIdentifier !== '' && DataHandler::doesKeyExists($subIdentifier, $arEntitiesTwoEntity)) {
                        $identifierKey = (DataHandler::doesKeyExists('id', $arEntitiesTwoEntity[$subIdentifier])) ? 'id' : 'slug';
		            	$arEntitiesTwoEntityId = $arEntitiesTwoEntity[$subIdentifier][$identifierKey];
		            } else {
                        $identifierKey = (DataHandler::doesKeyExists('id', $arEntitiesTwoEntity)) ? 'id' : 'slug';
			            $arEntitiesTwoEntityId = $arEntitiesTwoEntity[$identifierKey];
		            }
	            }

	            if ($arEntitiesTwoEntityId === $arEntitiesOneEntityId) {
	            	$change = false;
	            }
            }

            if ($change === true) {
                $result[] = $arEntitiesOneEntity;
            }
        }

        return $result;
    }
}
