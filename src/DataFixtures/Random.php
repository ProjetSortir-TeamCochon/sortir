<?php


namespace App\DataFixtures;


use DateTimeInterface;

abstract class Random
{
    // Generic Sets
    public static $digitSet = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");

    public static $lcCharSet = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "x", "y", "z");

    public static $ucCharSet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "X", "Y", "Z");

    public static $domainExtSet = array("com", "fr", "be", "io", "dev", "org", "net");

    // Sortie Entity sets

    public static $sortieTypeSet = array("Fête", "Sortie Velo", "Ballade", "Promenade", "Randonnée",
        "Soirée", "Exposition", "Concert", "Festival", "Projection", "Escalade", "Apero");

    public static $dummyTextSet = array(
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sint ista Graecorum; Dat enim intervalla et relaxat. Quo igitur, inquit, modo? Duo Reges: constructio interrete. Haec para/doca illi, nos admirabilia dicamus. Haec igitur Epicuri non probo, inquam.",
        "Sed fac ista esse non inportuna; Idem adhuc; Suam denique cuique naturam esse ad vivendum ducem. Respondeat totidem verbis.",
        "Avaritiamne minuis? Illud non continuo, ut aeque incontentae. Minime vero, inquit ille, consentit. Scisse enim te quis coarguere possit? Sed haec omittamus; An hoc usque quaque, aliter in vita? Quae cum dixisset, finem ille. Ut aliquid scire se gaudeant?",
        "Facillimum id quidem est, inquam. Paria sunt igitur. Nunc vides, quid faciat. Istic sum, inquit.",
        "Paulum, cum regem Persem captum adduceret, eodem flumine invectio? Non laboro, inquit, de nomine. Bonum liberi: misera orbitas.",
        "Et non ex maxima parte de tota iudicabis? In schola desinis. Laboro autem non sine causa; Tecum optime, deinde etiam cum mediocri amico. Omnia contraria, quos etiam insanos esse vultis. Paulum, cum regem Persem captum adduceret, eodem flumine invectio?",
        "Eam stabilem appellas. Iubet igitur nos Pythius Apollo noscere nosmet ipsos. Quis est tam dissimile homini. Minime vero, inquit ille, consentit. Falli igitur possumus. Itaque hic ipse iam pridem est reiectus;",
        "Cum id fugiunt, re eadem defendunt, quae Peripatetici, verba. Cur haec eadem Democritus? Eaedem res maneant alio modo. Illa videamus, quae a te de amicitia dicta sunt. Tubulo putas dicere?",
        "Quid igitur, inquit, eos responsuros putas? Quid enim? ",
        "Quibusnam praeteritis? Quid censes in Latino fore? Praeteritis, inquit, gaudeo. Istic sum, inquit. Recte, inquit, intellegis. Ergo illi intellegunt quid Epicurus dicat, ego non intellego?"
        );

    // Generic Utilities

    public static function randomFromSet(array $array)
    {
        return $array[array_rand($array)];
    }

    public static function randomSlice(array $array) : array
    {
        $offset = array_rand($array);
        $length = rand($offset, sizeof($array) - 1);
        return array_slice($array, $offset, $length);
    }

    public static function charSetFromRules(bool $digit, bool $lowerCase, bool $upperCase): array
    {
        $charSet = array();
        if($digit) $charSet = array_merge($charSet, Random::$digitSet);
        if($lowerCase) $charSet = array_merge($charSet, Random::$lcCharSet);
        if($upperCase) $charSet = array_merge($charSet, Random::$ucCharSet);

        return $charSet;
    }

    public static function string(int $size,array $charSet): string
    {
        if($size <= 0) return "";

        $output = "";
        for($i = 0; $i < $size; $i++){
            $output = $output.$charSet[array_rand($charSet)];
        }

        return $output;
    }

    public static function boolean():bool
    {
        return (bool) rand(0,1);
    }

    public static function float($min = 0, $max = 1) {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    public static function text(): string
    {
        return implode("\n", self::randomSlice(self::$dummyTextSet));
    }

    public static function dateTime(\DateTimeInterface $minDate, \DateTimeInterface $maxDate) : \DateTimeInterface
    {
        $diff = $maxDate->diff($minDate, true);
        $date = new \DateTime();
        $rand_Interval = new \DateInterval('P'
            .rand(0,$diff->days).'D'
            .'T'.rand(9,22).'H'
            .rand(0,59).'M'
        );
        $rand_Date = $minDate;
        $rand_Date->add($rand_Interval);
        return $rand_Date;
    }

    // For User Entity
    public static function email():string
    {
        $charSet = self::charSetFromRules(true, true, true);
        return self::string(rand(5, 20), $charSet)
            ."@"
            .self::string(rand(5,10), $charSet)
            ."."
            .self::$domainExtSet[array_rand(self::$domainExtSet)];
    }

    public static function pseudo():string
    {
        return self::$ucCharSet[array_rand(self::$ucCharSet)].self::string(rand(5, 10), self::$lcCharSet);
    }
}