<?php

namespace DataDrivenDesign\VoiceDesigner;

use DataDrivenDesign\VoiceDesigner\Options\AppJson;
use DataDrivenDesign\VoiceDesigner\Options\SiteGuid;
use WP_REST_Response;

class RestRoutes
{
    private const URI_NAMESPACE = 'ddd-voice-designer/v1';

    private static $routes = [
        ['method' => 'GET', 'path' => '/app', 'callback' => 'getApp'],
        ['method' => 'POST', 'path' => '/app', 'callback' => 'saveApp'],
        ['method' => 'POST', 'path' => '/app/versions', 'callback' => 'publishApp'],
        ['method' => 'GET', 'path' => '/search', 'callback' => 'searchPosts'],
    ];

    public static function register()
    {
        add_action('rest_api_init', [new static, 'initializeRoutes']);
    }

    public function initializeRoutes()
    {
        $groupedRoutes = $this->groupRoutes();
        $permissions = function () {
            return current_user_can('edit_posts');
        };

        foreach ($groupedRoutes as $path => $route) {
            register_rest_route(static::URI_NAMESPACE, $path, [
                'methods' => array_keys($route),
                'callback' => function ($request) use ($route) {
                    $method = strtoupper($request->get_method());
                    $callback = $route[$method];

                    return call_user_func_array([$this, $callback], [$request]);
                },
                'permission_callback' => $permissions,
            ]);
        }
    }

    private function groupRoutes()
    {
        $groupedRoutes = [];

        foreach (static::$routes as $route) {
            [
                'path' => $path,
                'method' => $method,
                'callback' => $callback
            ] = $route;

            if (!isset($groupedRoutes[$path])) {
                $groupedRoutes[$path] = [];
            }

            $groupedRoutes[$path][strtoupper($method)] = $callback;
        }

        return $groupedRoutes;
    }

    public function getApp()
    {
        return AppJson::get() ?: null;
    }

    public function saveApp($request)
    {
        try {
            AppJson::set($request->get_json_params() ?? null);
        } catch (ValidationException $ex) {
            return rest_ensure_response(new WP_REST_Response($ex->getErrors(), 422));
        }
    }

    public function publishApp()
    {
        BackendApi::getInstance()->publishApp(SiteGuid::get(), get_site_url());
    }

    public function searchPosts($request)
    {
        $params = $request->get_query_params();
        $results = [];

        $posts = get_posts([
            'posts_per_page' => $params['per_page'] ?? 25,
            'post_type' => ['post', 'page'],
            's' => $params['search'],
        ]);

        $results = array_map(function ($post) {
            return [
                'id' => $post->ID,
                'title' => $post->post_title,
                'speech' => VoiceContentMeta::getFor($post->ID),
            ];
        }, $posts);

        return rest_ensure_response($results);
    }
}
