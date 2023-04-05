<?php

    namespace Boodschappenservice\attributes;

    use Attribute;
    use Boodschappenservice\utilities\RegExp;

    #[Attribute(Attribute::TARGET_PROPERTY)]
    class Column {

        public ?RegExp $regexp;

        public function __construct(
            public string $name,
            public bool $primary = false,
            public bool $nullable = false,
            string $regexp = null,
            public int $minLength = 0,
            public int|float $maxLength = INF,
            public bool $immutable = false,
            public bool $sensitive = false,
            public int|float $min = -INF,
            public int|float $max = INF
        ) {
            $this->regexp = !empty($regexp) ? RegExp::compile($regexp) : null;
        }

    }