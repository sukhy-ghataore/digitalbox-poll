<?php

namespace WP\Rest;

class Api
{
    public function __construct()
    {
        add_action("rest_api_init", [$this, "init_routes"]);
    }
    /**
     * Init routes
     */
    public function init_routes()
    {
        // poll api
        $this->route("POST", "api/v1", "/poll/submit", [
            "\WP\Rest\PollApi",
            "handle_poll_submission",
        ]);
    }
    /**
     * Register a new rest route
     *
     * @param string $method HTTP method GET/POST etc
     * @param string $namespace Namespace for route
     * @param string $route The route
     * @param callable $callback Callback function/method
     */
    public function route(
        string $method,
        string $namespace,
        string $route,
        array $callback
    ) {
        register_rest_route($namespace, $route, [
            "methods" => $method,
            "callback" => [new ($callback[0])(), $callback[1]],
            "permission_callback" => function () {
                return true;
            },
        ]);
    }
}
