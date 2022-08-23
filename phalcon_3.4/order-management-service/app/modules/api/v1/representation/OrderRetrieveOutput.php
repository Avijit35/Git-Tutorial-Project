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
 * Order
 * This description is related to Order
 */
class OrderRetrieveOutput
{
    /** @var int The description is related to id of the order */
    public $orderId;

    /** @var string The description is related to the order price */
    public $orderPrice;

    /** @var int The description is related to attributes of order quantity */
    public $orderQuantity;

    /** @var string The description is related to attributes of order status */
    public $orderStatus;

    /** @var string The description is related to attributes of order date */
    public $orderDate;

    /** @var array */
    public $productDetails;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
}
