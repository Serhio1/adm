<?php

namespace App\Entity;

abstract class CrudEntity
{
    /**
     * this fields will show by default in entityView page
     * needs for getter method
     */
    public $publicFields = array(
        'id'
    );

    /**
     * @return mixed
     */
    public function getPublicFields()
    {
        return $this->publicFields;
    }

    /**
     * @param mixed $publicFields
     */
    public function setPublicFields($publicFields): void
    {
        return;
        //$this->publicFields = $publicFields;
    }


}