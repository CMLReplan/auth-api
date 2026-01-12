<?php

namespace App\EventListener;


use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;


class ApiKeyListener
{
    private string $apiKey;


    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        // DEBUG LOGS — ADD HERE
        error_log('--- HEADERS ---');
        error_log(json_encode($request->headers->all()));

        error_log('--- SERVER ---');
        error_log(json_encode($request->server->all()));

        $providedKey = $request->headers->get('X-API-KEY')
        ?? $request->headers->get('x-api-key')
        ?? $request->server->get('HTTP_X_API_KEY');

        error_log('Provided API Key: ' . var_export($providedKey, true));
        error_log('Expected API Key: ' . $this->apiKey);

        if ($providedKey !== $this->apiKey) {
            $event->setResponse
                (new JsonResponse(['error' => 'Unauthorized. Invalid API Key.'
                ], 401)
            );
        }
    }
}