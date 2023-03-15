<?php

namespace App\Command;

use App\Entity\Character;
use App\Entity\Location;
use App\Entity\Origin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import-characters',
    description: 'Import all Characters from Rick and Morty API',
)]
class ImportCharactersCommand extends Command
{
    private SymfonyStyle $io;
    private $client;
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;

        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->section('Importing Characters from Rick and Morty API');
        $this->io->progressStart(826);
        $this->recursiveImport('https://rickandmortyapi.com/api/character');

        $this->entityManager->flush();

        $this->io->progressFinish();
        $this->io->success("All Characters imported");

        return Command::SUCCESS;
    }

    protected function recursiveImport($url) {

        $response = $this->client->request(
            'GET',
            $url
        );

        $data = json_decode($response->getContent());

        foreach ($data->results as $characterData) {
            $this->io->progressAdvance();

            $characterEntity = new Character();
            $characterEntity->setName($characterData->name);
            $characterEntity->setStatus($characterData->status);
            $characterEntity->setSpecies($characterData->species);
            $characterEntity->setType($characterData->type);
            $characterEntity->setGender($characterData->gender);
            $characterEntity->setImage($characterData->image);

            //check if Origin already exists
            $origin = $this->entityManager->getRepository(Origin::class)->findOneBy(['name' => $characterData->origin->name]);
            if ($origin) {
                $characterEntity->setOrigin($origin);
            } else {
                $characterOrigin = new Origin();
                $characterOrigin->setName($characterData->origin->name);
                $characterOrigin->setUrl($characterData->origin->url);

                $this->entityManager->persist($characterOrigin);
                $this->entityManager->flush();

                $characterEntity->setOrigin($characterOrigin);
            }

            //check if Location already exists
            $location = $this->entityManager->getRepository(Location::class)->findOneBy(['name' => $characterData->location->name]);
            if ($location) {
                $characterEntity->setLocation($location);
            } else {
                $characterLocation = new Location();
                $characterLocation->setName($characterData->location->name);
                $characterLocation->setUrl($characterData->location->url);

                $this->entityManager->persist($characterLocation);
                $this->entityManager->flush();

                $characterEntity->setLocation($characterLocation);
            }

            $this->entityManager->persist($characterEntity);
        }

        if (isset($data->info->next)) {
            $this->recursiveImport($data->info->next);
        }
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command import all Characters from Rick and Morty API');
    }
}