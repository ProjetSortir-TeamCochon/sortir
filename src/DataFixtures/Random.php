<?php


namespace App\DataFixtures;


abstract class Random
{
    public static $digitSet = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");

    private static $lcCharSet = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "x", "y", "z");

    private static $ucCharSet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "X", "Y", "Z");

    private static $domainExtSet = array("com", "fr", "be", "io", "dev", "org", "net");

    public static function charSetFromRules($digit, $lowerCase, $upperCase): array
    {
        $charSet = array();
        if($digit) $charSet = array_merge($charSet, Random::$digitSet);
        if($lowerCase) $charSet = array_merge($charSet, Random::$lcCharSet);
        if($upperCase) $charSet = array_merge($charSet, Random::$ucCharSet);

        return $charSet;
    }

    public static function string(int $size, $charSet): string
    {
        if($size <= 0) return "";

        $output = "";
        for($i = 0; $i < $size; $i++){
            $output = $output.array_rand($charSet);
        }

        return $output;
    }

    public static function email():string
    {
        $charSet = self::charSetFromRules(true, true, true);
        return self::string(rand(5, 20), $charSet)
            ."@"
            .self::string(rand(5,10), $charSet)
            ."."
            .array_rand(self::$domainExtSet);
    }

    public static function pseudo():string
    {
        return array_rand(self::$ucCharSet).self::string(rand(5, 10), self::$lcCharSet);
    }

    public static function boolean():bool
    {
        return (bool) rand(0,1);
    }
}