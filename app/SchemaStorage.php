<?php

namespace Absolvent\swagger;

use JsonSchema\SchemaStorage as BaseSchemaStorage;
use JsonSchema\UriResolverInterface;
use JsonSchema\UriRetrieverInterface;

class SchemaStorage extends BaseSchemaStorage
{
    const INTERNAL_PROVIDED_SCHEMA_URI_DEFINITIONS = BaseSchemaStorage::INTERNAL_PROVIDED_SCHEMA_URI.'#/definitions';
    const FILE_PROVIDED_SCHEMA_URI = 'file://definitions';

    public function __construct(UriRetrieverInterface $uriRetriever = null, UriResolverInterface $uriResolver = null, SwaggerSchema $swaggerSchema)
    {
        parent::__construct($uriRetriever, $uriResolver);

        $this->addSchema(
            SchemaStorage::FILE_PROVIDED_SCHEMA_URI,
            $swaggerSchema->get('definitions')
        );
    }

    public function resolveRef($ref)
    {
        if (starts_with($ref, self::INTERNAL_PROVIDED_SCHEMA_URI)) {
            // in this case normalize the ref name to fetch global schema
            // instead of local variant for which JSON schema is searching for
            return parent::resolveRef(str_replace(
                self::INTERNAL_PROVIDED_SCHEMA_URI_DEFINITIONS,
                SchemaStorage::FILE_PROVIDED_SCHEMA_URI.'#',
                $ref
            ));
        }

        return parent::resolveRef($ref);
    }
}
