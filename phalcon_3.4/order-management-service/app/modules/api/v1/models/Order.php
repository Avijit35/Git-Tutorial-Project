<?php

namespace Service\Modules\Api\V1\Models;

class Order extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $order_id;

    /**
     *
     * @var double
     */
    public $order_price;

    /**
     *
     * @var integer
     */
    public $order_quantity;

    /**
     *
     * @var string
     */
    public $order_status;

    /**
     *
     * @var string
     */
    public $order_date;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("order_management_db");
        $this->setSource("order");
        $this->hasMany('order_id', 'Payment', 'order_id', ['alias' => 'Payment']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'order';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Order[]|Order|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Order|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}

