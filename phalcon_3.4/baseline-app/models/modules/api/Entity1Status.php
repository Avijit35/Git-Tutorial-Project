<?php

namespace App\Modules\Api\Models;

class Entity1Status extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var string
     */
    public $entity_name;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("entity1_status");
        $this->hasMany('id', 'App\Modules\Api\Models\Entity1', 'status', ['alias' => 'Entity1']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'entity1_status';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Entity1Status[]|Entity1Status|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Entity1Status|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
