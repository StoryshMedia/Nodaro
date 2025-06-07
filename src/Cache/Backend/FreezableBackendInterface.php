<?php

namespace Smug\Core\Cache\Backend;

/**
 * A contract for a cache backend which can be frozen.
 */
interface FreezableBackendInterface extends BackendInterface
{
    public function freeze();

    /**
     * @return bool
     */
    public function isFrozen();
}
