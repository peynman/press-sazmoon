<?php

namespace Larapress\SAzmoon\Services\Azmoon;

use Illuminate\Contracts\Queue\ShouldQueue;
use Larapress\CRUD\Events\CRUDVerbEvent;
use Larapress\CRUD\Services\RBAC\IPermissionsMetadata;

class BuildAzmoonDetailsListener implements ShouldQueue
{
    public function hanlde(CRUDVerbEvent $event)
    {
        if (
            $event->verb === IPermissionsMetadata::EDIT ||
            $event->verb === IPermissionsMetadata::CREATE
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
