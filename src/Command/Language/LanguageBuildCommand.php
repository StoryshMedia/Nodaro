<?php

namespace Smug\Core\Command\Language;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class LanguageBuildCommand extends Command
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('language:message:build')
            ->setDescription('collects translation files and builds one file per language.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $assets = [];

        $finder = new Finder();
        $finder->directories()->in($this->kernel->getProjectDir() . "/bundle")->depth(0);

        $output->writeln('Collecting translations');
        $output->writeln('#####################');

        foreach ($finder as $namespace) {
            $bundleFinder = new Finder();
            $bundleFinder->directories()->in($namespace->getPathname())->depth(0);
            
            foreach ($bundleFinder as $bundle) {
                $output->writeln('Searching translations in: ' . $namespace->getBasename() . ' => ' . $bundle->getBasename());
                
                if (DataHandler::proofDir($bundle->getPathname() . '/assets/js/i18n')) {
                    $translationFinder = new Finder();
                    $translationFinder
                        ->files()
                        ->in($bundle->getPathname() . '/assets/js/i18n')
                        ->name(['*.json']);

                    foreach ($translationFinder as $translation) {
                        $languageName = DataHandler::getReplaceString('.json', '', $translation->getBasename());

                        if (!DataHandler::doesKeyExists($languageName, $assets)) {
                            $assets[$languageName] = [];
                        }

                        $languageTranslation = DataHandler::getJsonDecode(
                            DataHandler::getFile($translation->getPathname()),
                            true
                        );

                        $assets[$languageName] = DataHandler::mergeArray($languageTranslation, $assets[$languageName]);
                    }
                }
            }
        }
        
        $output->writeln('Done');
        $output->writeln('Writing translation Files');
        $output->writeln('#####################');

        foreach ($assets as $languageKey => $languageData) {
           DataHandler::writeFile($this->kernel->getProjectDir() . '/assets/js/modules/locales/' . $languageKey . '.json', DataHandler::getJsonEncodePretty($languageData)); 
        }

        $output->writeln('Done');
        $output->writeln('Complete: translations build');
	    
        return 0;
    }
}
