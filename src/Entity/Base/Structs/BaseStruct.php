<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base\Structs;

use Smug\Core\Entity\Base\Traits\GetValuesTrait;
use Smug\Core\Entity\Base\Traits\JsonSerializerTrait;

abstract class BaseStruct implements \JsonSerializable
{
    // // allows to assign array data to this object
    // use AssignArrayTrait;

    // // allows to clone full struct with all references
    // use CopyTrait;

    // // allows to create a new instance with all data of the provided object
    // use CreateFromTrait;
    
    use JsonSerializerTrait;

    use GetValuesTrait;
}
