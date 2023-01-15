<?php

namespace Rodrifarias\PasswordGenerator\Exception;

class IgnoreCharacterException extends PasswordGeneratorException
{
    public function __construct(string $type)
    {
        parent::__construct("Not allowed ignore all characters of [$type]", 500);
    }
}
