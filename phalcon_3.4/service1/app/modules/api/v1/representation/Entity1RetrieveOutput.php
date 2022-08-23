<?php

namespace Service\Modules\Api\V1\Representation;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Structure\ClassStructure;


/**
 * Entity1
 * This description is related to Entity1
 */
class Entity1RetrieveOutput extends ClassStructure
{
    /** @var string The description is related to name of the entity */
    public $entityName;

    /** @var string The description is related to attributes of entity */
    public $entityAttr;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->entityName = Schema::string();
        $properties->entityName->description = "The description is related to name of the entity";
        $properties->entityAttr = Schema::string();
        $properties->entityAttr->description = "The description is related to attributes of entity";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->schema = "http://json-schema.org/draft-07/schema#";
        $ownerSchema->title = "Entity1";
        $ownerSchema->description = "This description is related to Entity1";
        $ownerSchema->id = "public/schemas/entity1.retrieve.output.schema.json";
    }
}
