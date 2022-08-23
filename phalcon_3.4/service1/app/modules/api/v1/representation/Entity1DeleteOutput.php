<?php

namespace Service\Modules\Api\V1\Representation;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Structure\ClassStructure;


/**
 * Entity_1
 * This description is related to Entity1
 */
class Entity1DeleteOutput extends ClassStructure
{
    /** @var int The description is related to the return code */
    public $returnCode;

    /** @var string The description is related to the return status */
    public $returnStatus;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->returnCode = Schema::integer();
        $properties->returnCode->description = "The description is related to the return code";
        $properties->returnStatus = Schema::string();
        $properties->returnStatus->description = "The description is related to the return status";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->schema = "http://json-schema.org/draft-07/schema#";
        $ownerSchema->title = "Entity_1";
        $ownerSchema->description = "This description is related to Entity1";
        $ownerSchema->id = "public/schemas/entity1.delete.output.schema.json";
    }
}
