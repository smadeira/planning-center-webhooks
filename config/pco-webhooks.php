<?php

return [

    /*
     * Planning Center will sign webhooks using a secret. You can find the secret used at the webhook
     * configuration settings: https://api.planningcenteronline.com/webhooks for each webhook
     */
    'signing_secrets' => [
        env('PCO_DESTROY_SIGNING_SECRET'),
        env('PCO_CREATE_SIGNING_SECRET'),
        env('PCO_UPDATE_SIGNING_SECRET')
        ],

    /*
     * Here you can define the job that should be run when a certain webhook hits your
     * application.
     *
     * You can find a list of PCO webhook types here:
     * https://api.planningcenteronline.com/webhooks
     */
    'jobs' => [
        'personCreated' => \App\Jobs\HandlePcoPersonCreated::class,
        'personDestroyed' => \App\Jobs\HandlePcoPersonDestroyed::class,
        'personUpdated' => \App\Jobs\HandlePcoPersonUpdated::class,
    ],
];
