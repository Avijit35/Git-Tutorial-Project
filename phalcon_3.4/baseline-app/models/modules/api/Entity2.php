<?php

namespace App\Modules\Api\Models;

class Entity1 extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $entity_name;

    /**
     *
     * @var string
     */
    public $entity_attr;

    /**
     *
     * @var string
     */
    public $entity_extra_attr1;

    /**
     *
     * @var string
     */
    public $entity_extra_attr2;

    /**
     *
     * @var string
     */
    public $entity_extra_attr3;

    /**
     *
     * @var integer
     */
    public $entity_parent;

    /**
     *
     * @var string
     */
    public $created_date;

    /**
     *
     * @var string
     */
    public $updated_date;

    /**
     *
     * @var string
     */
    public $status;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("entity1");
        $this->belongsTo('entity_parent', 'App\Modules\Api\Models\Entity1Parent', 'id', ['alias' => 'Entity1Parent']);
        $this->belongsTo('status', 'App\Modules\Api\Models\Entity1Status', 'id', ['alias' => 'Entity1Status']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'entity1';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Entity1[]|Entity1|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Entity1|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
