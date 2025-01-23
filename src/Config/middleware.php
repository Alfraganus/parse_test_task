<?php

use Slim\App;

return static function(App $app):void {
    $app->addErrorMiddleware(true, false, false);
};
