<?php

use Smug\Core\Http\ApplicationFactory;

require dirname(__DIR__).'/config/bootstrap.php';

ApplicationFactory::createAndRun();

