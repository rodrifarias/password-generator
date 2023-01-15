<?php

namespace Tests\Unit;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Rodrifarias\PasswordGenerator\Exception\{CharactersLengthException, IgnoreCharacterException, InvalidLengthPasswordException};
use Rodrifarias\PasswordGenerator\Password;

class PasswordGeneratorTest extends TestCase
{
    public function testPasswordShouldBeNumberOfCharacters(): void
    {
        for ($i = 3; $i < 513; $i++) {
            $password = new Password('', $i, false, false, false, false);
            $this->assertSame($i, mb_strlen($password->generate()));
        }
    }

    public function testShouldGeneratePasswordWithIgnoredCharacters(): void
    {
        $password = new Password('',15, true, false, false, false, [1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $this->assertSame(str_repeat('0', 15), $password->generate());
    }

    /**
     * @dataProvider dataProviderMyCharacters
     */
    public function testShouldGeneratePasswordWithMyCharacters(string $characters): void
    {
        $charactersList = str_split($characters);
        $password = new Password($characters);
        $passwordGenerated = $password->generate();

        for ($i = 0; $i < mb_strlen($passwordGenerated); $i++) {
            $this->assertTrue(in_array($passwordGenerated[$i], $charactersList));
        }
    }

    public function dataProviderMyCharacters(): array
    {
        $faker = Factory::create();

        $abcLowerRange = range('a', 'z');
        $abcUpperRange = range('A', 'Z');
        $numbersRange = range(0, 9);
        $symbols = [
            '\\', '"', '!', '@', '#', '$', '%',
            '&', '*', '(', ')', '-', '_', '+',
            '=', '[', ']', '{', '}', '?', '/',
            ';', ':', '>', '<', '.', ',', '\'', '|'
        ];

        $values = [];

        for ($i = 0; $i < 500; $i++) {
            $value = array_merge(
                $faker->randomElements($abcLowerRange, 5),
                $faker->randomElements($abcUpperRange, 5),
                $faker->randomElements($numbersRange, 5),
                $faker->randomElements($symbols, 5),
            );

            $key = implode('', $faker->shuffle($value));
            $values[$key] = [$key];
        }

        return $values;
    }

    /**
     * @dataProvider dataProviderTypesPasswords
     */
    public function testPasswordShouldHaveOnlyType(string $regex, array $uses): void
    {
        for ($i = 1; $i < 500; $i++) {
            $length = rand(3, 512);
            $password = new Password('', $length, ...$uses);
            $passwordReplace = preg_replace($regex, '', $password->generate());
            $this->assertSame($length, mb_strlen($passwordReplace));
        }
    }

    public function dataProviderTypesPasswords(): array
    {
        return [
            'onlyNumbers' => ['/\D+/', [true, false, false, false]],
            'onlyLowercaseLetter' => ['/[\d[A-Z]+/', [false, true, false, false]],
            'onlyUppercaseLetter' => ['/[\d[a-z]+/', [false, false, true, false]],
            'onlySymbols' => ['/[a-zA-Z0-9]/', [false, false, false, true]],
        ];
    }

    public function testShouldGenerateInvalidLengthPasswordExceptionWhenLengthIsInvalid(): void
    {
        $this->expectException(InvalidLengthPasswordException::class);
        $this->expectExceptionMessage('Password must be between 3 and 512 characters');

        new Password(length: 2);
    }

    public function testShouldGenerateCharactersLengthExceptionWhenMyCharactersLengthIsInvalid(): void
    {
        $this->expectException(CharactersLengthException::class);
        $this->expectExceptionMessage('Characters must have minimum 5 character');

        new Password('as 2');
    }

    /**
     * @dataProvider dataProviderIgnoreAllCharactersOnlyType
     */
    public function testShouldGenerateIgnoreCharacterException(string $type, array $ignore, array $uses): void
    {
        $this->expectException(IgnoreCharacterException::class);
        $this->expectExceptionMessage('Not allowed ignore all characters of [' . $type . ']');

        new Password(
            useNumbers: $uses['numbers'] ?? false,
            useLowercaseLetters: $uses['lower'] ?? false,
            useUppercaseLetters: $uses['upper'] ?? false,
            useSymbols: $uses['symbol'] ?? false,
            ignoreCharacters: $ignore,
        );
    }

    public function dataProviderIgnoreAllCharactersOnlyType(): array
    {
        $symbols = [
            '\\', '"', '!', '@', '#', '$', '%',
            '&', '*', '(', ')', '-', '_', '+',
            '=', '[', ']', '{', '}', '?', '/',
            ';', ':', '>', '<', '.', ',', '\'', '|'
        ];

        return [
            'numbers' => ['numbers', range(0, 9), ['numbers' => true]],
            'lower' => ['lower', range('a', 'z'), ['lower' => true]],
            'upper' => ['upper', range('A', 'Z'), ['upper' => true]],
            'symbol' => ['symbol', $symbols, ['symbol' => true]],
        ];
    }
}
