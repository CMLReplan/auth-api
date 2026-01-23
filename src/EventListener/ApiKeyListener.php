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


        $providedKey = $request->headers->get('X-API-KEY');

        if ($providedKey !== $this->apiKey) {
            $event->setResponse
                (new JsonResponse(['error' => 'Unauthorized. Invalid API Key.'], 401)
            );
        }
    }
}