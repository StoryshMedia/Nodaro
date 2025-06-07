<?php

declare(strict_types=1);

namespace Smug\Core\Entity\Base\Traits;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use ReflectionClass;
use ReflectionProperty;
use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\Entity\Base\Attribute\Sort;
use Smug\Core\Events\Backend\Data\EntityFieldLoadedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Attribute\Groups;

trait GetValuesTrait
{
    protected ?EventDispatcherInterface $dispatcher = null;

    /**
     * @return array<string, mixed>
     */
    public function getVars(array $disAllowedFields = [], bool $getChildren = true, array $restrictions = []): array
    {
		$rc = new ReflectionClass($this);
        $result = [];
        $this->dispatcher = new EventDispatcher();
        
        foreach ($rc->getProperties() as $property) {
            $restricted = false;

            if ($property->getModifiers() !== 2) {
                continue;
            }

            if (DataHandler::isInArray($property->getName(), $disAllowedFields)) {
                continue;
            }

            foreach ($restrictions as $restriction) {
                if ($restriction->getField() === $property->getName()) {
                    if (!$restriction->check($this->__get($property->getName()))) {
                        $restricted = true;
                    }
                }
            }

            if ($restricted) {
                return [];
            }

            foreach ($property->getAttributes() as $attribute) {
                if ($attribute->getName() === Column::class) {
                    $result[$property->getName()] = self::processAdditional($this->__get($property->getName()), $property->getAttributes());
                    $this->dispatch($rc->getName(), $property->getName(), $result[$property->getName()]);
                    break;
                }
                if ($attribute->getName() === ManyToOne::class) {
                    if (DataHandler::isNull($this->__get($property->getName()))) {
                        $result[$property->getName()] = [];
                        $this->dispatch($rc->getName(), $property->getName(), $result[$property->getName()]);
                        break;
                    }
                    
                    if (self::convert($property, 'minimal')) {
                        $result[$property->getName()] = self::getMinimal($this->__get($property->getName()));
                        $this->dispatch($rc->getName(), $property->getName(), $result[$property->getName()]);
                        break;
                    }

                    $result[$property->getName()] = $this->__get($property->getName())->toArray();
                    $this->dispatch($rc->getName(), $property->getName(), $result[$property->getName()]);
                    break;
                }
                if ($attribute->getName() === OneToOne::class && $getChildren) {
                    $value = $this->__get($property->getName());
                    
                    if (!DataHandler::isEmpty($value)) {
                        if (self::convert($property, 'minimal')) {
                            $result[$property->getName()] = self::getMinimal($this->__get($property->getName()));
                            $this->dispatch($rc->getName(), $property->getName(), $result[$property->getName()]);
                            break;
                        }

                        $result[$property->getName()] = $value->toArray();
                        $this->dispatch($rc->getName(), $property->getName(), $result[$property->getName()]);
                        break;
                    }

                    $result[$property->getName()] = [];
                    $this->dispatch($rc->getName(), $property->getName(), $result[$property->getName()]);
                    break;
                }
                if (
                    ($attribute->getName() === ManyToMany::class && $getChildren || $attribute->getName() === OneToMany::class && $getChildren)
                ) {
                    if (self::convert($property, 'list') === true) {
                        $result[$property->getName()] = ArrayProvider::getObjectsAsArray($this->__get($property->getName()));

                        if (self::convert($property, 'nested') === true) {
                            $result[$property->getName()] = DataHandler::getTree($result[$property->getName()]);
                        }

                        if (DataHandler::doesKeyExists($property->getName(), $result)) {
                            $result[$property->getName()] = self::processAdditional($result[$property->getName()], $property->getAttributes());
                        }
                    }

                    if (self::convert($property, 'minimal') === true) {
                        $result[$property->getName()] =  [];

                        foreach ($this->__get($property->getName()) as $item) {
                            $result[$property->getName()][] = self::getMinimal($item);
                        }

                        if (DataHandler::doesKeyExists($property->getName(), $result)) {
                            $result[$property->getName()] = self::processAdditional($result[$property->getName()], $property->getAttributes());
                        }
                    }

                    $this->dispatch($rc->getName(), $property->getName(), $result[$property->getName()] ?? []);
                    break;
                }
            }
        }
        
        return $result;
    }

    private static function getMinimal($item) : array {
		$rc = new ReflectionClass($item);
        $result = [];

        foreach ($rc->getProperties() as $property) {
            if ($property->getModifiers() !== 2) {
                continue;
            }

            foreach ($property->getAttributes() as $attribute) {
                if ($attribute->getName() === Column::class) {
                    $result[$property->getName()] = self::processAdditional($item->__get($property->getName()), $property->getAttributes());
                    break;
                }
            }
        }

        return $result;
    }

    private static function convert(ReflectionProperty $property, string $argument) {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === Groups::class) {
                return DataHandler::isInArray($argument, $attribute->getArguments()[0]);
            }
        }
        return false;
    }

    private static function processAdditional($data, $attributes) {
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === Sort::class) {
                $data = DataHandler::getArrayValues(
                    DataHandler::sortItemsByField($data, $attribute->getArguments()[0])
                );
            }
        }

        return $data;
    }

    public function dispatch(string $class, string $field, mixed $data)
    {
        $event = new EntityFieldLoadedEvent($data, $class, $field);
        $this->dispatcher->dispatch($event, SystemEvents::FIELD_DATA_LOADED);
    
        return $event->getData();
    }
}
