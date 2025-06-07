<?php

namespace Smug\AdministrationBundle\Event;

final class SystemEvents
{
    public const DETAIL_VIEW_CREATED = 'smug.administration.bundle.detail.view.created';

    public const DATA_BEGIN_CREATE = 'smug.administration.bundle.data.begin.create';

    public const CONSTANTS_LOADED = 'smug.administration.bundle.data.constants.loaded';

    public const DATA_CREATED = 'smug.administration.bundle.data.created';

    public const DATA_PRE_CREATED = 'smug.administration.bundle.data.pre.created';

    public const DATA_PRE_UPDATE = 'smug.administration.bundle.data.pre.update';

    public const DATA_PRE_MAPPING = 'smug.administration.bundle.data.pre.mapped';

    public const FIELD_DATA_LOADED = 'smug.administration.bundle.field.data.loaded';

    public const DATA_UPDATED = 'smug.administration.bundle.data.updated';

    public const DATA_MODEL_LOADED = 'smug.administration.bundle.data.model.loaded';

    public const DATA_MODEL_LIST_LOADED = 'smug.administration.bundle.data.model.list.loaded';

    public const DATA_DELETED = 'smug.administration.bundle.data.deleted';
}
