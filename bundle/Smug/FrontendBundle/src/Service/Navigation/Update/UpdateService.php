<?php

namespace Smug\FrontendBundle\Service\Navigation\Update;

use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Service\BaseService;

class UpdateService extends BaseService
{
    public function saveTree(Context $context): array
    {
        foreach ($context->getRequestData() as $site) {
            $siteObject = $context->getEntityByIdentifier($site['id']);
            
            $siteObject->__set('parentId', $site['parentId']);

            $context->getEntityManager()->persist($siteObject);
            $context->getEntityManager()->flush();

            $this->saveChildren($site['children'], $context, $site['id']);
        }

        return [
            'success' => true
        ];
    }

    protected function saveChildren(array $children, Context $context, string $parentId): void
    {
        foreach ($children as $child) {
            $this->saveChild($child, $context, $parentId);

            $this->saveChildren($child['children'], $context, $child['id']);
        }
    }

    protected function saveChild(array $child, Context $context, string $parentId): void
    {
        $siteObject = $context->getEntityByIdentifier($child['id']);
        
        $siteObject->__set('parentId', $parentId);

        $context->getEntityManager()->persist($siteObject);
        $context->getEntityManager()->flush();
    }
}
