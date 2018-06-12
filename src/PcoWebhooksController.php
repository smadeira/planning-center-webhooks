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

        if (!isset($eventPayload['data'][0]['attributes']['name'])) {
            throw WebhookFailed::missingType($request);
        }

        $type = $this->determineWebhookType($eventPayload);

        $pcoWebhookCall = new PcoWebhookCall($eventPayload);

        event("pco-webhooks::{$type}", $pcoWebhookCall);

        $jobClass = $this->determineJobClass($type);

        if ($jobClass === '') {
            return;
        }

        if (!class_exists($jobClass)) {
            throw WebhookFailed::jobClassDoesNotExist($jobClass, $pcoWebhookCall);
        }

        dispatch(new $jobClass($pcoWebhookCall));
    }

    protected function determineJobClass(string $type): string
    {
        return config("pco-webhooks.jobs.{$type}", '');
    }

    /**
     * Take the PCO webhook name and return a camel-Case
     * representation to be used as the Job name because the
     * format from PCO will cause problems with Laravel
     *
     * Example: people.v2.events.person.created => personCreated
     *
     * @param $eventPayload
     * @return string
     */
    protected function determineWebhookType($eventPayload)
    {
        $nameParts =  explode('.', $eventPayload['data'][0]['attributes']['name']);

        $type = $nameParts[3] . ucfirst($nameParts[4]);
        return $type;
    }
}
