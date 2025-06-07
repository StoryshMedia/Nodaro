<?php declare(strict_types=1);

namespace Smug\Core\Twig\Attribute;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugAdminConfig extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('adminConfig', [$this, 'getAdminConfig']),
        ];
    }

    public function getAdminConfig(array $config, string $element, string $id = '', int $tab = 0): string
    {
        $tabString = '';

        if ($tab > 0) {
            $tabString = 'data-tab="' . $tab . '"';
        }

        if (DataHandler::isEmpty($element)) {
            return 'data-id=' . DataHandler::getReplaceString('"', '', $id) . ' ' . $tabString . ' data-version="' . DataHandler::getUniqueId($id) . '"';
        }

        if (DataHandler::isEmpty($config['contentEditable'] ?? []) || !($config['contentEditable'][$element] ?? false)) {
            return 'data-id="' . $id . '" data-is-content-editor data-content-variable="' . $element . '" ' . $tabString . ' data-version="' . DataHandler::getUniqueId($id) . '"';
        }

        return 'data-id="' . $id . '" ' . $tabString . ' data-is-content-editor data-content-variable="' . $element . '" contenteditable data-version="' . DataHandler::getUniqueId($id) . '"';   
    }
}