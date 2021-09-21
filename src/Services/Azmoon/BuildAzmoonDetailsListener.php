<?php

namespace Larapress\SAzmoon\Services\Azmoon;

use Illuminate\Contracts\Queue\ShouldQueue;
use Larapress\CRUD\Events\CRUDVerbEvent;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;

class BuildAzmoonDetailsListener implements ShouldQueue
{
    public function handle(CRUDVerbEvent $event)
    {
        if (
            ($event->verb === ICRUDVerb::EDIT ||
            $event->verb === ICRUDVerb::CREATE) &&
            $event->providerClass === config('larapress.ecommerce.routes.products.provider')
        ) {
            $object = $event->getModel();
            if (
                isset($object->data['types'][config('larapress.sazmoon.product_typename')]['file_id']) &&
                !is_null($object->data['types'][config('larapress.sazmoon.product_typename')]['file_id'])
            ) {
                /** @var IAzmoonService */
                $service = app(IAzmoonService::class);
                $service->buildAzmoonDetails($object);
            }
        }
    }
}
