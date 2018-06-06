<?php namespace Smadeira\PcoWebhooks;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Smadeira\PcoWebhooks\Exceptions\WebhookFailed;
use Smadeira\PcoWebhooks\Middlewares\VerifySignature;

class PcoWebhooksController extends Controller
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

        $pcoWebhookCall = new PcoWebhookCall($eventPayload);

        event("pco-webhooks::{$type}", $pcoWebhookCall);

        $jobClass = $this->determineJobClass($type);

        if ($jobClass === '') {
            return;
        }

        if (! class_exists($jobClass)) {
            throw WebhookFailed::jobClassDoesNotExist($jobClass, $pcoWebhookCall);
        }

        dispatch(new $jobClass($pcoWebhookCall));
    }

    protected function determineJobClass(string $type): string
    {
        return config("pco-webhooks.jobs.{$type}", '');
    }
}
