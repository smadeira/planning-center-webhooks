# Handle Planning Center webhooks in a Laravel application

[Planning Center](https://planningcenteronline.com) can notify your application of events using webhooks. This package can help you 
handle those webhooks. Out of the box it will verify the PCO signature of all incoming requests. You can easily define jobs 
or events that should be dispatched when specific events hit your app. Current support is for PErson Update, Create and Destroy.

This package will not handle what should be done after the webhook request has been validated and the right job or event is called.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
