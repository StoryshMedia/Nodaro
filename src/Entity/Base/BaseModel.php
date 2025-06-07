<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Ramsey\Uuid\Doctrine\UuidV7Generator;
use ReflectionNamedType;
use ReflectionProperty;
use Smug\Core\DataAbstractionLayer\SchemaExtensionBuilder;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Smug\Core\Entity\Base\Structs\BaseStruct;

#[MappedSuperclass]
#[HasLifecycleCallbacks]
class BaseModel extends BaseStruct
{
    #[Id]   
    #[Column(type: 'uuid', unique:true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidV7Generator::class)]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'ID',
        'config' => [
            'disabled' => true
        ]
    ])]
    protected string $id;

    public function __get($name)
    {        
        return $this->$name;
    }

    public function __set($name, $value)
    {
        if (!DataHandler::doesPropertyExist($this, $name)) {
            $field = SchemaExtensionBuilder::getSchemaExtensionForClassAndName(self::class, $name);
        } else {
            $field = new ReflectionProperty($this, $name);
        }

        if (!DataHandler::isEmpty($field)) {
            if (
                !DataHandler::isEmpty($field->getType()) &&
                DataHandler::isInstanceOf($field->getType(), ReflectionNamedType::class) &&
                $field->getType()->getName() === 'int'
            ) {
                $value = intval($value);
            }
            
            $this->$name = $value;
        }
    }

    public function __add($name, $value)
    {
        if (DataHandler::isEmpty($this->$name) || $this->$name->contains($value)) {
            return;
        }

        $this->$name->add($value);
    }

    public function __remove($name, $value)
    {
        if (!$this->$name->contains($value)) {
            return;
        }

        $this->$name->removeElement($value);
    }

    public function getId (): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function toArray(array $disAllowedFields = [], bool $getChildren = true, array $restrictions = []): array
    {
        return parent::getVars($disAllowedFields, $getChildren, $restrictions);
    }

    public function getListItem(): array
    {
        return $this->toArray();
    }

    public function getSelectedFieldsItem(array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            $result[$field] = $this->__get($field);
        }

        return $result;
    }

    public function getMainImage()
    {
        try {
            $images = $this->__get('images');

            foreach ($images as $image) {
                return $image->__get('media');
            }
        } catch (\Exception $ex) {
            return null;
        }

        return null;
    }

    public function getSerializer(): Serializer
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return new Serializer($normalizers, $encoders);
    }
}
