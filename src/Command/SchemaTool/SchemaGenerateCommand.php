<?php

declare(strict_types=1);

namespace Smug\Core\Command\SchemaTool;

use Doctrine\ORM\EntityManagerInterface;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Base\UserBaseModel;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SchemaGenerateCommand extends Command
{
    protected $em;
    protected $kernel;

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('smug:schema:generate')
            ->setDescription('Generates the proxy entity classes that are used by the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Collecting metadatas');
        $ui = new SymfonyStyle($input, $output);

        $metadatas = $this->getMedaData();

        if (DataHandler::isEmpty($metadatas)) {
            $ui->getErrorStyle()->success('No Metadata Classes to process.');
            return 0;
        }

        foreach ($metadatas as $notGenerated) {
            if ($notGenerated->name === BaseModel::class || $notGenerated->name === UserBaseModel::class) {
                continue;
            }

            EntityGenerator::generate($notGenerated->name);
        }
        
        $output->writeln('Successfully migrated ids to UUIDs!');
        return 1;
    }

    protected function getMedaData(): array {
        $result = [];
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();

        if (DataHandler::isEmpty($metadatas)) {
            return [];
        }

        foreach ($metadatas as $metadata) {
            if ($metadata->namespace !== 'Smug\Core\Entity\Generated') {
                $result[] = $metadata;
            }
        }

        return $result;
    }
}
