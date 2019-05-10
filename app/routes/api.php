<?php

/**
 * Register your api routes here ...
 */

// jv - jquery validation
$app->group('/jv', function() {
    $this->get('/email-exist', ["Auth\\JqueryValidationController", "emailExist"]);
})->add("Auth\\XhrMiddleware");
