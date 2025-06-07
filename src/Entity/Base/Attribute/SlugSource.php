<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base\Attribute;

use Attribute;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

#[Attribute]
class SlugSource {
    public array $sources;
    
    public function __construct(string $sources) {
        $this->sources = DataHandler::explodeArray(',', $sources);
    }
}