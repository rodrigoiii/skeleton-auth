<?php

/**
 * Register your web routes here ...
 */
$app->get('/', ["ExampleController", "index"]);

$app->group("/auth", function() {
    $this->group("/login", function() {
        $this->get("", ["Auth\\LoginController", "getLogin"])->setName("auth.login");
        $this->post("", ["Auth\\LoginController", "postLogin"]);
    })->add("Auth\\GuestMiddleware");

    $this->post("/logout", ["Auth\\LoginController", "logout"])
        ->setName("auth.logout")
        ->add("Auth\\UserMiddleware");

    $this->group('/forgot-password', function() {
        $this->get('', ["Auth\\ForgotPasswordController", "getForgotPassword"])->setName('auth.forgot-password');
        $this->post('', ["Auth\\ForgotPasswordController", "postForgotPassword"]);
    })->add("Auth\\GuestMiddleware");

    $this->group('/reset-password', function() {
        $this->get('/{token}', ["Auth\\ResetPasswordController", "getResetPassword"])->setName('auth.reset-password');
        $this->post('/{token}', ["Auth\\ResetPasswordController", "postResetPassword"]);
    })->add("Auth\\GuestMiddleware");

    $this->get("/home", ["Auth\\HomeController", "index"])
    ->setName("auth.home")
    ->add("Auth\\UserMiddleware");
});
