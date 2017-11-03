<?php

namespace Newride\swagger;

use JsonSchema\SchemaStorage as BaseSchemaStorage;
use JsonSchema\UriResolverInterface;
use JsonSchema\UriRetrieverInterface;

class SchemaStorage extends BaseSchemaStorage
{
    const SYMBOL_SCHEMA_ANCHOR = '#';
    const INTERNAL_PROVIDED_SCHEMA_URI_DEFINITIONS = BaseSchemaStorage::INTERNAL_PROVIDED_SCHEMA_URI.self::SYMBOL_SCHEMA_ANCHOR.'/definitions';
    const FILE_PROVIDED_SCHEMA_URI_DEFINITIONS = 'file://definitions';

    public function __construct(UriRetrieverInterface $uriRetriever = null, UriResolverInterface $uriResolver = null, SwaggerSchema $swaggerSchema)
    {
        parent::__construct($uriRetriever, $uriResolver);

        $this->addSchema(
            self::FILE_PROVIDED_SCHEMA_URI_DEFINITIONS,
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
                self::FILE_PROVIDED_SCHEMA_URI_DEFINITIONS.self::SYMBOL_SCHEMA_ANCHOR,
                $ref
            ));
        }

        if (starts_with($ref, self::FILE_PROVIDED_SCHEMA_URI_DEFINITIONS)) {
            // in this case the schema is nested in definitions list
            return static::resolveRef(str_replace(
                self::FILE_PROVIDED_SCHEMA_URI_DEFINITIONS,
                self::INTERNAL_PROVIDED_SCHEMA_URI,
                $ref
            ));
        }

        return parent::resolveRef($ref);
    }
}
