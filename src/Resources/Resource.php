<?php

namespace Cordial\Resources;

use Cordial\Cordial;

class Resource
{
    /**
     * The resource attributes.
     *
     * @var array
     */
    public $attributes;

    /**
     * The Cordial SDK instance.
     *
     * @var \Cordial\Cordial|null
     */
    protected $cordial;

    /**
     * Create a new resource instance.
     *
     * @param  array  $attributes
     * @param  \Cordial\Cordial|null  $cordial
     * @return void
     */
    public function __construct(array $attributes, Cordial $cordial = null)
    {
        $this->attributes = $attributes;
        $this->cordial = $cordial;

        $this->fill();
    }

    /**
     * Fill the resource with the array of attributes.
     *
     * @return void
     */
    protected function fill()
    {
        foreach ($this->attributes as $key => $value) {
            $key = $this->camelCase($key);

            $this->{$key} = $value;
        }
    }

    /**
     * Convert the key name to camel case.
     *
     * @param  string  $key
     * @return string
     */
    protected function camelCase($key)
    {
        $parts = explode('_', $key);

        foreach ($parts as $i => $part) {
            if ($i !== 0) {
                $parts[$i] = ucfirst($part);
            }
        }

        return str_replace(' ', '', implode(' ', $parts));
    }

    /**
     * Transform the items of the collection to the given class.
     *
     * @param  array  $collection
     * @param  string  $class
     * @param  array  $extraData
     * @return array
     */
    protected function transformCollection(array $collection, $class, array $extraData = [])
    {
        return array_map(function ($data) use ($class, $extraData) {
            return new $class($data + $extraData, $this->cordial);
        }, $collection);
    }
}