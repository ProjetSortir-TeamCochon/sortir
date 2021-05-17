<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\Boolean;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

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
                rand(5,10), Random::charSetFromRules(false, false, true))
            );
        if($admin) $user->setRoles(['ROLE_USER']);
        else $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        return $user;
    }
}
