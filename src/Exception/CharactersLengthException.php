<?php

namespace Rodrifarias\PasswordGenerator\Exception;

class CharactersLengthException extends PasswordGeneratorException
{
    protected $message = 'Characters must have minimum 5 character';
}
