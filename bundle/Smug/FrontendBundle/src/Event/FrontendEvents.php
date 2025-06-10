<?php

namespace Smug\FrontendBundle\Event;

final class FrontendEvents
{
    public const FRONTEND_PAGE_LOADED = 'smug.frontend.bundle.site.loaded';
    public const FRONTEND_PAGE_CONTENT_LOADED = 'smug.frontend.bundle.site.content.loaded';
    public const FRONTEND_ITEM_SELECTION_SECTION_LOADED = 'smug.frontend.bundle.item.selection.section.loaded';
    public const FRONTEND_ITEM_TEASER_TEMPLATE_LOADED = 'smug.frontend.bundle.item.teaser.template.loaded';
    public const FRONTEND_PLUGIN_SETTINGS_LOADED = 'smug.frontend.bundle.plugin.settings.loaded';
    public const FRONTEND_CONTENT_ITEM_LOADED = 'smug.frontend.bundle.content.item.loaded';
    public const FRONTEND_PAGE_SEO_DATA_LOADED = 'smug.frontend.bundle.page.seo.data.loaded';
    public const FRONTEND_API_ENDPOINTS_LOADED = 'smug.frontend.bundle.api.endpoints.loaded';
    public const FRONTEND_CONTENT_ITEM_MODULE_DATA_LOADED = 'smug.frontend.bundle.content.item.module.data.loaded';
}
