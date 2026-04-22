<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\Company;
use App\Entity\JobOffer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Admin
        $admin = new User();
        $admin->setEmail('admin@jobboard.dev');
        $admin->setFirstName('Admin');
        $admin->setLastName('JobBoard');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setPlainPassword('Admin1234!');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin1234!'));
        $manager->persist($admin);

        // Users
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email());
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setPlainPassword('Password123!');
            $user->setPassword($this->passwordHasher->hashPassword($user, 'Password123!'));
            $manager->persist($user);
            $users[] = $user;
        }

        // Companies
        $companies = [];
        $sectors = ['Tech', 'Finance', 'Santé', 'Éducation', 'E-commerce', 'Gaming', 'Startup'];
        $cities = ['Paris', 'Lyon', 'Bordeaux', 'Nantes', 'Toulouse', 'Marseille', 'Remote'];

        for ($i = 0; $i < 15; $i++) {
            $company = new Company();
            $company->setName($faker->company());
            $company->setDescription($faker->paragraph());
            $company->setWebsite('https://' . $faker->domainName());
            $company->setCity($faker->randomElement($cities));
            $company->setSector($faker->randomElement($sectors));
            $manager->persist($company);
            $companies[] = $company;
        }

        // Job Offers
        $techStacks = ['PHP/Symfony', 'Node.js/React', 'Python/Django', 'Java/Spring', 'Vue.js/Laravel', 'Go/Kubernetes', 'TypeScript/NestJS'];
        $titles = ['Développeur Backend', 'Lead Developer', 'DevOps Engineer', 'Full Stack Developer', 'Software Architect', 'Data Engineer'];
        $contractTypes = JobOffer::CONTRACT_TYPES;

        $jobOffers = [];
        for ($i = 0; $i < 30; $i++) {
            $offer = new JobOffer();
            $offer->setTitle($faker->randomElement($titles));
            $offer->setDescription($faker->paragraphs(3, true));
            $offer->setContractType($faker->randomElement($contractTypes));
            $offer->setCity($faker->randomElement($cities));
            $offer->setIsRemote($faker->boolean(40));
            $offer->setTechStack($faker->randomElement($techStacks));
            $offer->setSalaryMin($faker->numberBetween(35000, 65000));
            $offer->setSalaryMax($faker->numberBetween(65000, 100000));
            $offer->setIsActive($faker->boolean(80));
            $offer->setCompany($faker->randomElement($companies));
            $manager->persist($offer);
            $jobOffers[] = $offer;
        }

        // Applications
        for ($i = 0; $i < 20; $i++) {
            $application = new Application();
            $application->setCandidate($faker->randomElement($users));
            $application->setJobOffer($faker->randomElement($jobOffers));
            $application->setStatus($faker->randomElement(Application::STATUSES));
            $application->setCoverLetter($faker->paragraph(4));
            $manager->persist($application);
        }

        $manager->flush();
    }
}