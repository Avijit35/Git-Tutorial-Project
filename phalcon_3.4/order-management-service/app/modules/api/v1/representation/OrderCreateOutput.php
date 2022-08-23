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
class OrderCreateOutput
{
    /** @var int The description is related to the return code */
    public $returnCode;

    /** @var string The description is related to the return status */
    public $returnStatus;

    /** @var int The description is related to id of the order */
    public $orderId;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
}
