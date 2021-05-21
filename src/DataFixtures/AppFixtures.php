<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
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
        // Etat
        $etats = array();
        foreach (Etat::libelles as $libelle)
        {
            $manager->persist( (new Etat())->setLibelle($libelle) );
        }
        $manager->flush();

        //Ville
        $villes = array(
            (new Ville())->setNom("Paris 06")->setCodePostal("75006"),
            (new Ville())->setNom("Paris 15")->setCodePostal("75015"),
            (new Ville())->setNom("Paris 08")->setCodePostal("75008"),
            (new Ville())->setNom("Lyon 01")->setCodePostal("69001"),
            (new Ville())->setNom("Lyon 02")->setCodePostal("69002"),
            (new Ville())->setNom("Marseille 06")->setCodePostal("13006"),
            (new Ville())->setNom("Bordeaux")->setCodePostal("33100"),
            (new Ville())->setNom("Bordeaux")->setCodePostal("33100"),
            (new Ville())->setNom("Caen")->setCodePostal("14000"),
            (new Ville())->setNom("Toulouse")->setCodePostal("31100"),
            (new Ville())->setNom("Rennes")->setCodePostal("35200"),
            (new Ville())->setNom("Nantes")->setCodePostal("44200")
        );

        // Campus
        $campus = array(
            (new Campus())->setNom("Paris"),
            (new Campus())->setNom("Lyon"),
            (new Campus())->setNom("Marseille"),
            (new Campus())->setNom("Rennes"),
            (new Campus())->setNom("Nantes"),
            (new Campus())->setNom("Bordeaux")
        );

        // Lieu
        // -> Associé à une ville
        $lieux = array(
            (new Lieu())->setNom("Chateau Cochon")->setRue("36 rue du Chateau")
                        ->setLatitude(Random::float(1, 90))->setLongitude(Random::float(1, 90))
                        ->setVille(Random::randomFromSet($villes)),
            (new Lieu())->setNom("Bar Bertie La Truie")->setRue("23 impasse de Bertie")
                        ->setLatitude(Random::float(1, 90))->setLongitude(Random::float(1, 90))
                        ->setVille(Random::randomFromSet($villes)),
            (new Lieu())->setNom("Forêt des Sangliers")->setRue("RF 42")
                        ->setLatitude(Random::float(1, 90))->setLongitude(Random::float(1, 90))
                        ->setVille(Random::randomFromSet($villes)),
            (new Lieu())->setNom("Salle de jeu Groink Groink")->setRue("666 rue de la Republique")
                        ->setLatitude(Random::float(1, 90))->setLongitude(Random::float(1, 90))
                        ->setVille(Random::randomFromSet($villes)),
            (new Lieu())->setNom("Charcuterie Aubongras")->setRue("35 petite rue tranquille")
                        ->setLatitude(Random::float(1, 90))->setLongitude(Random::float(1, 90))
                        ->setVille(Random::randomFromSet($villes)),
            (new Lieu())->setNom("Le Mur de Groins")->setRue("4 ZI du Cochon")
                        ->setLatitude(Random::float(1, 90))->setLongitude(Random::float(1, 90))
                        ->setVille(Random::randomFromSet($villes)),
            (new Lieu())->setNom("Roseraie Rose Bouchon")->setRue("Avenue de l'Europe")
                        ->setLatitude(Random::float(1, 90))->setLongitude(Random::float(1, 90))
                        ->setVille(Random::randomFromSet($villes)),
            (new Lieu())->setNom("Salle de spectacle Le Tire Bout'd'Son")->setRue("rue du 11 novembre")
                        ->setLatitude(Random::float(1, 90))->setLongitude(Random::float(1, 90))
                        ->setVille(Random::randomFromSet($villes)),
            (new Lieu())->setNom("MJC des Gorets")->setRue("123 avenue des 3 petits cochons")
                        ->setLatitude(Random::float(1, 90))->setLongitude(Random::float(1, 90))
                        ->setVille(Random::randomFromSet($villes))
        );

        // User
        // -> Associé à un campus
        $users = array();
        for($n = 0; $n < 50; $n++){
            $user = $this->makeUser(false)->setCampus(Random::randomFromSet($campus));
            array_push($users, $user);
        }
        array_push($users, $this->makeUser(true));
        array_push($users, $this->makeUser(true));

        // Sortie
        // -> Associée à un user organisateur
        // -> Associée à un campus
        // -> Inscrire des users au hasard (attention limite d'inscriptions)
        // -> Associe a un lieu
        $sorties = array();
        for($n = 0; $n < 50; $n++){
            $organisateur = Random::randomFromSet($users);
            $lieu = Random::randomFromSet($lieux);

            $sortie = $this->makeSortie()->setCampus(Random::randomFromSet($campus))
                            ->setOrganisateur($organisateur)
                            ->addUser($organisateur)
                            ->setLieu($lieu)
                            ->setNom(Random::randomFromSet(Random::$sortieTypeSet).' à '.$lieu->getNom());

            $nbInscrits = mt_rand(2,rand(2, $sortie->getNbInscriptionsMax()-1));
            $inscrits = $sortie->getUsers();
            while(sizeof($inscrits) < $nbInscrits){
              $inscrit = Random::randomFromSet($users);
              if(!array_search($inscrit, $inscrits->toArray())) {
                  $sortie->addUser($inscrit);
              }
            };
            array_push($sorties, $sortie);
        }

        foreach($villes as $ville){
            $manager->persist($ville);
        }
        foreach($campus as $camp){
            $manager->persist($camp);
        }
        foreach($etats as $etat){
            $manager->persist($etat);
        }
        foreach ($lieux as $lieu){
            $manager->persist($lieu);
        }
        foreach ($users as $user){
            $manager->persist($user);
        }
        foreach ($sorties as $sortie)
        {
            $manager->persist($sortie);
        }

        $manager->flush();
    }

    // USER Creation
    private function makeUser(bool $admin): User
    {
        $user = new User();
        $user->setAdministrateur($admin)
            ->setUsername(Random::pseudo())
            ->setMail(Random::email())
            ->setActif(Random::boolean())
            ->setPrenom(Random::pseudo())
            ->setNom(Random::string(
                rand(5,10), Random::$ucCharSet)
            )
            ->setTelephone(Random::string(10, Random::$digitSet))
            ->setPassword("enienieni");

        if(!$admin){
            $user->setRoles(['ROLE_USER']);
        }
        else {
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        }

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

        $sortie->setNbInscriptionsMax(rand(4,25))
            ->setDuree(rand(60, 240))
            ->setInfosSortie(Random::text())
            ->setDateHeureDebut($randomDate)
            ->setDateLimiteInscription(
                (new \DateTime($randomDate->format(DATE_ISO8601)))->sub($twoDays)
                );
        return $sortie;
    }
}
