<?php

namespace OhDear\LaravelWebhooks;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OhDear\LaravelWebhooks\Exceptions\WebhookFailed;
use OhDear\LaravelWebhooks\Middlewares\VerifySignature;

class OhDearWebhooksController extends Controller
{
    public function __construct()
    {
        $this->middleware(VerifySignature::class);
    }

    public function __invoke(Request $request)
    {
        $eventPayload = json_decode($request->getContent(), true);

        if (! isset($eventPayload['type'])) {
            throw WebhookFailed::missingType($request);
        }

        $type = $eventPayload['type'];

        $ohDearWebhookCall = new OhDearWebhookCall($eventPayload);

        event("ohdear-webhooks::{$type}", $ohDearWebhookCall);

        $jobClass = $this->determineJobClass($type);

        if ($jobClass === '') {
            return;
        }

        if (! class_exists($jobClass)) {
            throw WebhookFailed::jobClassDoesNotExist($jobClass, $ohDearWebhookCall);
        }

        dispatch(new $jobClass($ohDearWebhookCall));
    }

    protected function determineJobClass(string $type): string
    {
        return config("ohdear-webhooks.jobs.{$type}", '');
    }
}
