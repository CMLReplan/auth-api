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

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Only protect API routes
        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        // Read Authorization header from ALL possible sources
        $authHeader =
            $request->headers->get('Authorization')
            ?? $request->server->get('HTTP_AUTHORIZATION')
            ?? $_SERVER['HTTP_AUTHORIZATION']
            ?? getenv('HTTP_AUTHORIZATION')
            ?? null;

        $providedKey = null;

        // Expected format: Authorization: ApiKey your_api_key_here
        if ($authHeader && str_starts_with($authHeader, 'ApiKey ')) {
            $providedKey = substr($authHeader, 7);
        }

        if ($providedKey !== $this->apiKey) {
            $event->setResponse
                (new JsonResponse(['error' => 'Unauthorized. Invalid API Key.'
                ], 401)
            );
        }
    }
}