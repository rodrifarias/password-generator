<?php

namespace Rodrifarias\PasswordGenerator\Exception;

class InvalidLengthPasswordException extends PasswordGeneratorException
{
    protected $message = 'Password must be between 3 and 512 characters';
}
