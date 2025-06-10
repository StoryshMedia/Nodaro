<?php

namespace Smug\FrontendBundle\Service\Frontend\Slug;

use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Preparer\Slugify\Slugify;
use Smug\FrontendBundle\Entity\Site\Site;

class SlugService
{
    public function create(Context $context): string
    {
        $site = $context->getMainEntity();

        if ($site->__get('rootPage') === true) {
            return '/';
        }

        return $this->getSlugByParents($site, $context);
    }

    protected function getSlugByParents(Site $site, Context $context): string
    {
        $slug = '';

        if (!DataHandler::isEmpty($site->__get('parentId'))) {
            $parentSite = $context->getEntityByIdentifier($site->__get('parentId'));

            $newSlug = $this->getSlugByParents($parentSite, $context);
            $slug .= $newSlug;
        }
        
        if (!DataHandler::isEmpty($site->__get('slug'))) {
            $slug .= $site->__get('slug');
        } else {
            $slug .= Slugify::slugify($site->__get('title'));
        }


        return DataHandler::getReplaceString('//', '/', $slug);
    }
}
