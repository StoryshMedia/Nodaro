<?php

namespace Smug\AdministrationBundle\Event;

final class SearchEvents
{
    public const GET_SEARCH_CORE_LIST = 'smug.administration.bundle.search.core.list.loading';

    public const BACKEND_SEARCH_START = 'smug.administration.bundle.backend.search.start';

    public const SEARCH_START = 'smug.administration.bundle.search.start';

    public const PERFORM_SEARCH = 'smug.search.perform';

    public const GLOBAL_SEARCH_START = 'smug.global.administration.bundle.search.start';

    public const GLOBAL_PERFORM_SEARCH = 'smug.global.administration.perform';
}
