<?php

namespace Cordial\Resources;

class Contact extends Resource
{
    /**
     * The id of the contact.
     *
     * @var int
     */
    public $id;

    /**
     * Delete the given certificate.
     *
     * @return void
     */
    public function delete()
    {
        $this->cordial->deleteContact($this->id);
    }
}
