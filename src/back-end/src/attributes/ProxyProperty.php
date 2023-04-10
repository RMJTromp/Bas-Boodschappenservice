<?php

    namespace Boodschappenservice\attributes;

    use Attribute;

    #[Attribute(Attribute::TARGET_PROPERTY)]
    class ProxyProperty {

        public function __construct(
            public string $propertyName
        ) {}

    }