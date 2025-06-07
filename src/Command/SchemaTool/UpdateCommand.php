<?php

declare(strict_types=1);

namespace Smug\Core\Command\SchemaTool;

use Doctrine\ORM\EntityManagerInterface;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\DataAbstractionLayer\SchemaTool;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Base\UserBaseModel;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateCommand extends Command
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
        $this->setName('smug:database:update')
            ->setDescription('Executes (or dumps) the SQL needed to update the database schema to match the current mapping metadata');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Collecting metadatas');
        $schemaTool = new SchemaTool($this->em, $this->kernel);
        $ui = new SymfonyStyle($input, $output);

        $metadatas = $this->getMedaData();

        if (DataHandler::isEmpty($metadatas)) {
            $ui->getErrorStyle()->success('No Metadata Classes to process.');
            return 0;
        }

        foreach ($metadatas['notGenerated'] as $notGenerated) {
            if ($notGenerated->name === BaseModel::class || $notGenerated->name === UserBaseModel::class) {
                continue;
            }

            EntityGenerator::generate($notGenerated->name);
        }
        $metadatas = $this->getMedaData();
        
        $schemaTool->updateSchema($metadatas['generated']);
        
        $output->writeln('Successfully migrated ids to UUIDs!');
        return 1;
    }

    protected function getMedaData(): array {
        $result = [
            'generated' => [],
            'notGenerated' => []
        ];
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();

        if (DataHandler::isEmpty($metadatas)) {
            return [];
        }

        foreach ($metadatas as $metadata) {
            if ($metadata->namespace !== 'Smug\Core\Entity\Generated') {
                $result['notGenerated'][] = $metadata;
            } else {
                $result['generated'][] = $metadata;
            }
        }

        return $result;
    }
}
