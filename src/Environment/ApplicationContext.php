<?php

namespace Smug\Core\Environment;

use Exception;

class ApplicationContext
{
    protected $contextString;

    protected $rootContextString;

    protected $parentContext;

    public function __construct($contextString)
    {
        if (!str_contains($contextString, '/')) {
            $this->rootContextString = $contextString;
            $this->parentContext = null;
        } else {
            $contextStringParts = explode('/', $contextString);
            $this->rootContextString = $contextStringParts[0];
            array_pop($contextStringParts);
            $this->parentContext = new self(implode('/', $contextStringParts));
        }

        if (!in_array($this->rootContextString, ['Development', 'Production', 'Testing'], true)) {
            throw new Exception('The given context "' . $contextString . '" was not valid. Only allowed are Development, Production and Testing, including their sub-contexts', 1335436551);
        }

        $this->contextString = $contextString;
    }

    public function __toString()
    {
        return $this->contextString;
    }

    public function isDevelopment()
    {
        return $this->rootContextString === 'Development';
    }

    public function isProduction()
    {
        return $this->rootContextString === 'Production';
    }

    public function isTesting()
    {
        return $this->rootContextString === 'Testing';
    }

    public function getParent()
    {
        return $this->parentContext;
    }
}
