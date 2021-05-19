<?php

namespace App\DataFixtures;

use App\Entity\Sortie;
use App\Entity\User;
use DateTimeInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /*
     * Commande pour peupler la BDD avec tout ce qui exécuté dans load()
     * > php bin/console doctrine:fixtures:load
     */

    public function load(ObjectManager $manager)
    {
        for($n = 0; $n < 50; $n++){
            $manager->persist($this->makeUser(false));
            $manager->persist($this->makeSortie());
        }
        $manager->persist($this->makeUser(true));
        $manager->persist($this->makeUser(true));

        $manager->flush();
    }


    // USER Creation
    private function makeUser(bool $admin): User
    {
        $user = new User();
        $user->setAdministrateur($admin)
            ->setMail(Random::email())
            ->setActif(Random::boolean())
            ->setPrenom(Random::pseudo())
            ->setNom(Random::string(
                rand(5,10), Random::$ucCharSet)
            )
            ->setTelephone(Random::string(10, Random::$digitSet))
            ->setPassword("enienieni");

        if(!$admin) $user->setRoles(['ROLE_USER']);
        else $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        return $user;
    }

    private function makeAdminUser(): User
    {
        return $this->makeUser(true);
    }

    // SORTIE Creation
    private function makeSortie(): Sortie
    {
        $sortie = new Sortie();
        $aYear = new \DateInterval("P1Y");

        $minusAYear = (new \DateTime())->sub($aYear);
        $plusAYear = (new \DateTime())->add($aYear);
        $twoDays = new \DateInterval("P2D");
        $randomDate = Random::dateTime($minusAYear, $plusAYear);


        $sortie->setNom(Random::nomSortie())
            ->setNbInscriptionsMax(rand(4,25))
            ->setDuree(rand(60, 240))
            ->setInfosSortie(Random::text())
            ->setEtat(0)
            ->setDateHeureDebut($randomDate)
            ->setDateLimiteInscription(
                (new \DateTime($randomDate->format(DATE_ISO8601)))->sub($twoDays)
                );
        return $sortie;
    }
}
