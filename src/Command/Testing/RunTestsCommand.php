<?php

namespace Smug\Core\Command\Testing;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class RunTestsCommand extends Command
{
    private KernelInterface $kernel;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('tests:run')
            ->setDescription('Runs all PHP Unit tests in each Bundle');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = new Finder();
        $finder->directories()->depth(0)->in($this->kernel->getProjectDir() . "/bundle");

        foreach ($finder as $file) {
            $bundleFinder = new Finder();
            $bundleFinder->directories()->depth(0)->in($this->kernel->getProjectDir() . "/bundle" . DIRECTORY_SEPARATOR . $file->getFilename());

            foreach ($bundleFinder as $bundle) {
                $output->writeln([
                    '<info>' . 'Testing in bundle' . DIRECTORY_SEPARATOR . $file->getFilename() . DIRECTORY_SEPARATOR . $bundle->getFilename() . '</>',
                    '<info>==========================</>',
                    '',
                ]);
                $process = new Process(['./vendor/bin/phpunit', 'bundle' . DIRECTORY_SEPARATOR . $file->getFilename() . DIRECTORY_SEPARATOR . $bundle->getFilename()]);
                $process->setTimeout(3600);
                $process->run();
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
                echo $process->getOutput();
                $output->writeln([
                    '',
                    '<info>==========================</>'
                ]);
            }
        }
        $output->writeln('Done');

        $output->writeln('Complete: Alls Tests were excecuted');
	    
        return 0;
    }
}
