# Password Generator

A simple password generator

# Installation
```
composer require rodrifarias/password-generator
```

# Creating a new password

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Rodrifarias\PasswordGenerator\Password;

$password = new Password();
echo $password->generate() . PHP_EOL;
```

# Password Create Config

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Rodrifarias\PasswordGenerator\Password;

// With length between 5 and 512 characters
$passwordLength = new Password(
    length: 12
);

// With my characters
$passwordWithMyCharacters = new Password(
    characters: 'abc123-!@WXC'
);

// Ignore characters
$passwordIgnoreCharacters = new Password(
    ignoreCharacters: ['a', '2', '@', '-', 'R']
);

// Only numbers
$passwordOnlyNumbers = new Password(
    useLowercaseLetters: false,
    useUppercaseLetters: false,
    useSymbols: false,
);

// Only lowercase letters
$passwordOnlyLowercaseLetters = new Password(
    useNumbers: false,
    useUppercaseLetters: false,
    useSymbols: false,
);

// Only uppercase letters
$passwordOnlyUppercaseLetters = new Password(
    useNumbers: false,
    useLowercaseLetters: false,
    useSymbols: false,
);

// Only symbols
$passwordOnlySymbols = new Password(
    useNumbers: false,
    useLowercaseLetters: false,
    useUppercaseLetters: false,
);

echo $passwordLength->generate() . PHP_EOL;
echo $passwordWithMyCharacters->generate() . PHP_EOL;
echo $passwordIgnoreCharacters->generate() . PHP_EOL;
echo $passwordOnlyNumbers->generate() . PHP_EOL;
echo $passwordOnlyLowercaseLetters->generate() . PHP_EOL;
echo $passwordOnlyUppercaseLetters->generate() . PHP_EOL;
echo $passwordOnlySymbols->generate() . PHP_EOL;
```

# It's possible to create combinations, for example:
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Rodrifarias\PasswordGenerator\Password;

// Password with only numbers, lowercase letters and 64 characters
$password = new Password(
    length: 64,
    useUppercaseLetters: false,
    useSymbols: false,
);

echo $password->generate() . PHP_EOL;
```


