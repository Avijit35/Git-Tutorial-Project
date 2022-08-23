<?php

namespace Service\Modules\Api\V1\Models;

class Payment extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $payment_id;

    /**
     *
     * @var string
     */
    public $payment_type;

    /**
     *
     * @var string
     */
    public $payment_method;

    /**
     *
     * @var double
     */
    public $payment_amount;

    /**
     *
     * @var integer
     */
    public $order_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("order_management_db");
        $this->setSource("payment");
        $this->belongsTo('order_id', 'Order', 'order_id', ['alias' => 'Order']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'payment';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Payment[]|Payment|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Payment|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
