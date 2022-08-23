<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace Service\Modules\Api\V1\Representation;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Structure\ClassStructure;


/**
 * Payment
 * This description is related to Payment
 */
class PaymentRetrieveOutput
{
    /** @var int The description is related to id of the product */
    public $productId;

    /** @var string The description is related to name of the payment type */
    public $paymentType;

    /** @var string The description is related to attributes of payment method */
    public $paymentMethod;

    /** @var string The description is related to attributes of payment amount */
    public $paymentAmount;

    /** @var int The description is related to id of the order */
    public $orderId;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
}
