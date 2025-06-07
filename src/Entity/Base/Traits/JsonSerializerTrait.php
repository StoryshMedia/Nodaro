<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base\Traits;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

trait JsonSerializerTrait
{
    /**
     * @return array<array-key, mixed>
     */
    public function jsonSerialize(): array
    {
        $vars = get_object_vars($this);
        
        $this->convertDateTimePropertiesToJsonStringRepresentation($vars);

        return $vars;
    }

    /**
     * @param array<string, mixed> $array
     */
    protected function convertDateTimePropertiesToJsonStringRepresentation(array &$array): void
    {
        foreach ($array as &$value) {
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format(\DateTime::RFC3339_EXTENDED);
            }
            if (DataHandler::isString($value)) {
                if (DataHandler::isArray(DataHandler::getJsonDecode($value, true))) {
                    $value = DataHandler::getJsonDecode($value, true);
                }
            }
        }
    }
}
