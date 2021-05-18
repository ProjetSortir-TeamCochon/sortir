<?php

namespace App\DataFixtures;

use App\Entity\User;
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
        for($n = 0; $n < 20; $n++){
            $manager->persist($this->makeUser(false));
        }

        $manager->persist($this->makeUser(true));
        $manager->persist($this->makeUser(true));

        $manager->flush();
    }

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

        if($admin) $user->setRoles(['ROLE_USER']);
        else $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        return $user;
    }

    private function makeAdminUser(): User{
        return $this->makeUser(true);
    }
}
