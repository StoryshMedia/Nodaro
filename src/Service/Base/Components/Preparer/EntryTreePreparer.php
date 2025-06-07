<?php

namespace Smug\Core\Service\Base\Components\Preparer;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

class EntryTreePreparer
{
    public function getEntryTree(&$entries, string $parentId = ''): array
    {
        $branch = [];

        foreach ($entries as $entry) {
            if ($entry['parentId'] == $parentId) {
                $children = $this->getEntryTree($entries, $entry['id']);

                if ($children) {
                    $entry['children'] = $children;
                } else {
                    $entry['children'] = [];
                }

                $branch[] = $entry;
            }
        }

        return $branch;
    }

    public function getFlattenEntryTree(array $entries, $parentId = null, array $flattenTree = [])
    {
        foreach ($entries as $key => $entry) {
            $flattenTree[] = [
                'id' => $entry['id'],
                'name' => $entry['name'],
                'position' => $key,
                'parent' => $parentId
            ];

            if (DataHandler::doesKeyExists('children', $entry)) {
                foreach ($entry['children'] as $k => $child) {
                    $flattenTree[] = [
                        'id' => $child['id'],
                        'name' => $child['name'],
                        'position' => $k,
                        'parent' => $entry['id']
                    ];

                    if (DataHandler::doesKeyExists('children', $child)) {
                        $flattenTree = $this->getFlattenEntryTree($child['children'], $child['id'], $flattenTree);
                    }
                }
            }
        }

        return $flattenTree;
    }
}
