<?php

namespace DataDrivenDesign\VoiceDesigner;

use Httpful\Request;

class BackendApi
{
    private static $instance = null;

    private function __construct()
    {
        $template = Request::init()
            ->sendsAndExpects('application/json');

        Request::ini($template);
    }

    public static function getInstance() : self
    {
        if (static::$instance) {
            return static::$instance;
        }

        return static::$instance = new static;
    }

    public function createApp()
    {
        $request = Request::post($this->uri('apps'));

        return $this->makeRequest($request)->guid;
    }

    public function saveApp($guid, $app)
    {
        $request = Request::put($this->uri("apps/{$guid}"), $app);

        return $this->makeRequest($request);
    }

    public function publishApp($guid, $siteUrl)
    {
        $request = Request::post($this->uri("apps/{$guid}/versions"), [
            'url' => $siteUrl,
        ]);

        return $this->makeRequest($request);
    }

    private function makeRequest(Request $request)
    {
        $response = $request->send();

        if ($response->hasErrors()) {
            ddd_voice_logger($response);
            throw new ValidationException($response);
        }

        return $response->body;
    }

    private function uri(string $endpoint) : string
    {
        $base = getenv('DDD_VOICE_BACKEND_API') ?: 'https://app.thevoicedesigner.com/api/';

        return "{$base}{$endpoint}";
    }
}
