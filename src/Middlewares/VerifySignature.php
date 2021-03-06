<?php namespace Smadeira\PcoWebhooks\Middlewares;

use Closure;
use Smadeira\PcoWebhooks\Exceptions\WebhookFailed;

class VerifySignature
{
    public function handle($request, Closure $next)
    {
        $signature = $request->header('X-PCO-Webhooks-Authenticity');

        if (! $signature) {
            throw WebhookFailed::missingSignature();
        }

        if (! $this->isValid($signature, $request->getContent())) {
            throw WebhookFailed::invalidSignature($signature);
        }

        return $next($request);
    }

    protected function isValid(string $signature, string $payload): bool
    {
        $secrets = config('pco-webhooks.signing_secrets');

        if ( ! is_array($secrets)) {
            throw WebhookFailed::signingSecretNotSet();
        }


        // Get values from the secrets name (temporary until this is rolled out
        // TODO - GSM  This is a HUGE kluge until old PCO tool is retired    
        $parameters = config('pco-webhooks.signing_secrets');
        foreach($parameters as $parameter){
            $secrets[] = getenv($parameter);
        }            
        
        // With multiple secrets we need to check each of them against the incoming message
        foreach($secrets as $secret){
            $computedSignature = hash_hmac('sha256', $payload, $secret);

            if ( hash_equals($signature, $computedSignature) ) return true;
        }

        // None of the secrets produced a valid response
        return false;
    }
}
