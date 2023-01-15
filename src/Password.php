<?php

namespace Rodrifarias\PasswordGenerator;

use Rodrifarias\PasswordGenerator\Exception\{CharactersLengthException, IgnoreCharacterException, InvalidLengthPasswordException};

class Password
{
    private const NUMBERS = '0123456789';
    private const LOWER_CASE_LETTERS = 'abcdefghijklmnopqrstuvwxyz';
    private const UPPER_CASE_LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const SYMBOLS = '\'"!@#$%&*()-_+=[]{}?\/;:><.,|';

    /** @var Array<int|string> */
    private array $ignoreCharacters;

    /**
     * @throws InvalidLengthPasswordException
     * @throws CharactersLengthException
     * @throws IgnoreCharacterException
     */
    public function __construct(
        private string $characters = '',
        private int $length = 32,
        private bool $useNumbers = true,
        private bool $useLowercaseLetters = true,
        private bool $useUppercaseLetters = true,
        private bool $useSymbols = true,
        array $ignoreCharacters = [],
    ) {
        $this->ignoreCharacters = array_filter($ignoreCharacters, fn ($i) => is_int($i) || is_string($i));

        $isValidLength = $this->length > 2 && $this->length < 513;

        if (!$isValidLength) {
            throw new InvalidLengthPasswordException();
        }

        if ($this->characters && mb_strlen(trim($this->characters)) < 5) {
            throw new CharactersLengthException();
        }

        $this->checkIgnoredCharacters();
    }

    public function generate(): string
    {
        $charactersPassword = $this->characters ?: self::getAllCharacters();
        $password = '';

        while (mb_strlen($password) < $this->length) {
            $character = match (true) {
                (bool)$this->characters => $this->getRandomCharacter($this->characters),
                $this->useNumbers && !preg_match('/[0-9]+/', $password) => $this->getRandomCharacter(self::NUMBERS),
                $this->useLowercaseLetters && !preg_match('/[a-z]+/', $password) => $this->getRandomCharacter(self::LOWER_CASE_LETTERS),
                $this->useUppercaseLetters && !preg_match('/[A-Z]+/', $password) => $this->getRandomCharacter(self::UPPER_CASE_LETTERS),
                $this->useSymbols && !preg_match('/[\'"!@#$%&*()-_=+\[\]{}?\/;:><.,|]/', $password) => $this->getRandomCharacter(self::SYMBOLS),
                default => $this->getRandomCharacter($charactersPassword)
            };

            $password .= str_replace($this->ignoreCharacters, '', $character);
        }

        return $password;
    }

    /**
     * @throws IgnoreCharacterException
     */
    private function checkIgnoredCharacters(): void
    {
        $numbers = str_split(self::NUMBERS);
        $lowerLetters = str_split(self::LOWER_CASE_LETTERS);
        $upperLetters = str_split(self::UPPER_CASE_LETTERS);
        $symbols = str_split(self::SYMBOLS);

        $containsAll = [
            'numbers' => ['total' => 0, 'totalDefault' => count($numbers)],
            'lower' => ['total' => 0, 'totalDefault' => count($lowerLetters)],
            'upper' => ['total' => 0, 'totalDefault' => count($upperLetters)],
            'symbol' => ['total' => 0, 'totalDefault' => count($symbols)],
        ];

        $ignoredCharacters = array_filter($this->ignoreCharacters, fn ($i) => is_numeric($i) || is_string($i));

        foreach ($ignoredCharacters as $ignoreCharacter) {
            match (true) {
                is_numeric($ignoreCharacter) && $this->useNumbers && in_array($ignoreCharacter, $numbers) => $containsAll['numbers']['total']++,
                $this->useLowercaseLetters && in_array($ignoreCharacter, $lowerLetters) => $containsAll['lower']['total']++,
                $this->useUppercaseLetters && in_array($ignoreCharacter, $upperLetters) => $containsAll['upper']['total']++,
                $this->useSymbols && in_array($ignoreCharacter, $symbols) => $containsAll['symbol']['total']++,
            };

        }

        foreach ($containsAll as $type => $item) {
            if ($item['total'] === $item['totalDefault']) {
                throw new IgnoreCharacterException($type);
            }
        }
    }

    private function getRandomCharacter(string $characters): string
    {
        $characterLengthToKey = mb_strlen($characters) - 1;
        $randKey = rand(0, $characterLengthToKey);
        return $characters[$randKey];
    }

    private function getAllCharacters(): string
    {
        $useRandomCharacters = !$this->useNumbers && !$this->useLowercaseLetters && !$this->useUppercaseLetters && !$this->useSymbols;
        $characters = self::NUMBERS . self::LOWER_CASE_LETTERS . self::UPPER_CASE_LETTERS . self::SYMBOLS;

        if (!$useRandomCharacters) {
            $listCharacters = [
                [$this->useNumbers, self::NUMBERS],
                [$this->useLowercaseLetters, self::LOWER_CASE_LETTERS],
                [$this->useUppercaseLetters, self::UPPER_CASE_LETTERS],
                [$this->useSymbols, self::SYMBOLS],
            ];

            $filterListCharacters = array_filter($listCharacters, fn ($l) => $l[0]);
            $charactersMap = array_map(fn ($c) => $c[1], $filterListCharacters);
            $characters = implode('', $charactersMap);
        }

        return $characters;
    }
}
