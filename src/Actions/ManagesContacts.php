<?php

namespace Cordial\Actions;

use Cordial\Resources\Contact;

trait ManagesContacts
{
    /**
     * Get the collection of contacts.
     *
     * @param  int  $serverId
     * @return \Cordial\Resources\Contact[]
     */
    public function contacts()
    {
        return $this->transformCollection(
            $this->get("contacts"),
            Contact::class
        );
    }

    /**
     * Get a job instance.
     *
     * @param  int  $serverId
     * @param  int  $contactId
     * @return \Cordial\Resources\Contact
     */
    public function contact($primaryKey)
    {
        return new Contact(
            $this->get("contacts/$primaryKey"),
            $this
        );
    }

    /**
     * Create a new job.
     *
     * @param  int  $serverId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Cordial\Resources\Contact
     */
    public function createContact($primaryKey, array $data, $wait = true)
    {
        $contact = $this->post("contacts", $data);

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($primaryKey, $contact) {
                $contact = $this->contact($primaryKey);

                return $contact->message == 'contact created' ? $contact : null;
            });
        }

        return new Contact($contact, $this);
    }

    /**
     * Delete the given job.
     *
     * @param  int  $serverId
     * @param  int  $contactId
     * @return void
     */
    public function deleteContact($contactId)
    {
        $this->delete("contacts/$contactId");
    }
}
