<?php

namespace Smug\FrontendBundle\Service\Frontend\Renderer;

use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class FieldEnricher
{
    public static function getContentEditableFields(array $module, array $tabs = []): array
    {
        $result = [];

        foreach ($module['fields'] ?? $module['module']['fields'] as $field) {
            if (!DataHandler::doesKeyExists('config', $field)) {
                continue;
            }

            if (DataHandler::isString($field['config'])) {
                $field['config'] = DataHandler::getJsonDecode($field['config'], true);
            }

            if ($field['config']['contentEditable'] ?? false === true) {
                $result[$field['identifier']] = true;
            }
        }

        if (!DataHandler::isEmpty($tabs)) {
            return DataHandler::mergeArray(
                $result,
                self::getContentEditableFields(DataHandler::getFirstArrayElement($tabs))
            );
        }

        return $result;
    }

    public static function enrichFields(array $module, array $moduleConfigFile, Context $context): array
    {
        $result = [];

        foreach ($module['fields'] ?? $module['module']['fields'] as $field) {
            if ($field['isPlugin'] ?? false === true) {
                continue;
            }

            $value = $field['value'] ?? $field['defaultValue'] ?? '';

            if (!DataHandler::isEmpty($field['files'])) {
                $value = $field['files'];
            }
            if ($field['type'] === 'FileUpload') {
                $value = $field['files'];

                if (!DataHandler::isEmpty($field['value'])) {
                    $value = DataHandler::getJsonDecode($field['value'], true);

                    foreach ($value as $key => $media) {
                        $queryBuilder = $context->getEntityManager()->createQueryBuilder();
                        $imageData = $queryBuilder
                            ->select('s')
                            ->from(EntityGenerator::getGeneratedEntity(Media::class), 's')
                            ->where('s.id = :id')
                            ->setParameter('id', $media['media']['id'])
                            ->getQuery()
                            ->getSingleResult();

                        $media['media'] = $context->getSerialized($imageData);

                        $value[$key] = $media;
                    }
                }
            }

            if (DataHandler::isArray($field['classes'])) {
                $classes = DataHandler::implodeArray(' ', $field['classes']);
            } else {
                $classes = DataHandler::implodeArray(' ', self::getBackupFieldClasses($field['identifier'], $moduleConfigFile));
            }

            if (DataHandler::isString($field['settings'])) {
                $field['settings'] = DataHandler::getJsonDecode($field['settings'], true);
            }
            
            $result[$field['identifier']] = [
                'value' => $value,
                'settings' => $field['settings'],
                'classes' => $classes
            ];
        }

        return $result;
    }

    public static function enrichPluginFields(array $module, Context $context): array
    {
        $result = [];

        foreach ($module['fields'] ?? $module['module']['fields'] as $field) {
            if ($field['isPlugin'] === false) {
                continue;
            }
            
            if ($field['type'] === 'FileUpload') {
                $value = DataHandler::getJsonDecode($field['value'], true);

                foreach ($value as $key => $media) {
                    if (DataHandler::isEmpty($media['media']['alternativeText'] ?? '')) {
                        $queryBuilder = $context->getEntityManager()->createQueryBuilder();
                        $imageData = $queryBuilder
                            ->select('s')
                            ->from(EntityGenerator::getGeneratedEntity(Media::class), 's')
                            ->where('s.id = :id')
                            ->setParameter('id', $media['media']['id'])
                            ->getQuery()
                            ->getSingleResult();

                        $media['media'] = $context->getSerialized($imageData);

                        $value[$key] = $media;
                    }
                }
            } else {
                $value = $field['value'] ?? $field['defaultValue'] ?? '';
            }

            if (DataHandler::isString($field['settings'])) {
                $field['settings'] = DataHandler::getJsonDecode($field['settings'], true);
            }
            
            if (DataHandler::isString($value)) {
                $result[$field['identifier']] = (DataHandler::getJsonDecode($value, true)) ? DataHandler::getJsonDecode($value, true) : $value;
            } else {
                $result[$field['identifier']] = $value;
            }
        }

        return $result;
    }

    public static function getBackupFieldClasses(string $identifier, array $moduleConfigFile): array
    {
        foreach ($moduleConfigFile['settings']['fields'] as $field) {
            if ($field['identifier'] === $identifier) {
                return $field['classes'];
            }
        }

        return [];
    }
}
