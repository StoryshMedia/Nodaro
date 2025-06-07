<?php

namespace Smug\Core\Service\System\Setting\Listing;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileContentProvider;
use Smug\Core\Service\Base\Service\ListBaseService;

class ListService extends ListBaseService
{
    public function getTemplate(array $data): array
    {
        return FileContentProvider::getTemplateFileContent($data['template'] . '.json', $data['mode']);
    }

    public function getRoutes(): array
    {
        $filePath = FileContentProvider::getSrcPath();

        return DataHandler::getJsonDecode(
            DataHandler::getFile($filePath . '../public/Resources/Data/Route/routes.json'),
            true
        );
    }
}
