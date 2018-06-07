<?php namespace Smadeira\PcoWebhooks\Exceptions;

use Exception;
use Illuminate\Http\Request;

class WebhookFailed extends Exception
{
    public static function missingSignature()
    {
        return new static('The request did not contain a header named `Pco-Signature`.');
    }

    public static function invalidSignature($signature)
    {
        return new static("The signature `{$signature}` found in the header named `X-PCO-Webhooks-Authenticity` is invalid. Make sure that the `pco-webhooks.signing_secret` config key is set to the value you found on the Planning Center dashboard. If you are caching your config try running `php artisan clear:cache` to resolve the problem.");
    }

    public static function signingSecretNotSet()
    {
        return new static('The PCO signing secret is not set. Make sure that the `pco-webhooks.signing_secrets` config key is set to the value you found on the PCO dashboard.');
    }

    public static function missingType(Request $request)
    {
        return new static('The webhook call did not contain a type. Valid PCO webhook calls should always contain a type.');
    }

    public function render($request)
    {
        return response(['error' => $this->getMessage()], 400);
    }
}
