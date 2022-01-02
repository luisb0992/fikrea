<?php

namespace App\Listeners;

use App\Events\DocumentRequireToBeSignedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DocumentRequireToBeSigned
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DocumentRequireToBeSignedEvent  $event
     * @return void
     */
    public function handle(DocumentRequireToBeSignedEvent $event)
    {
        //
    }
}
