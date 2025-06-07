<?php

namespace Smug\Core\Command\Frontend;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class FrontendBuildCommand extends Command
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('frontend:build')
            ->setDescription('collects all asset files and runs webpack encore.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Searching asset files');
        $output->writeln('#####################');

        $assets = [
        ];

        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . "/bundle")->name(['*.js']);

        $output->writeln('Done');
        $output->writeln('Collecting assets');
        $output->writeln('#####################');

        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'js')) {
                continue;
            }
            if (!DataHandler::isStringInString($file->getRelativePath(), 'modules')) {
                continue;
            }

            $assets[] = [
                'path' => $this->kernel->getProjectDir() . "/bundle/" . $file->getRelativePath() . '/' . $file->getFileName(),
                'module' => DataHandler::getCamelCaseString($file->getFileNameWithoutExtension(), '-')
            ];
        }
        
        $output->writeln('Done');
        $output->writeln('Writing Config File');
        $output->writeln('#####################');

        DataHandler::writeFile(__DIR__ . '/webpack-modules.json', DataHandler::getJsonEncode($assets));

        $output->writeln('Searching style files');
        $output->writeln('#####################');

        $assets = [
        ];

        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . "/bundle")->name(['*.scss']);

        $output->writeln('Done');
        $output->writeln('Collecting styles');
        $output->writeln('#####################');

        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'scss')) {
                continue;
            }
            if (!DataHandler::isStringInString($file->getRelativePath(), 'styles')) {
                continue;
            }


            $assets[] = [
                'path' => $this->kernel->getProjectDir() . "/bundle/" . $file->getRelativePath() . '/' . $file->getFileName(),
                'style' => self::getStylePrefix($file->getRelativePath()) . '-' . DataHandler::getCamelCaseString($file->getFileNameWithoutExtension(), '-')
            ];
        }
        
        $output->writeln('Done');
        $output->writeln('Writing styles File');
        $output->writeln('#####################');

        DataHandler::writeFile(__DIR__ . '/webpack-styles.json', DataHandler::getJsonEncode($assets));
        
        $assets = [];
        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . "/bundle")->name(['frontendFields.json']);

        $output->writeln('Done');
        $output->writeln('Collecting Frontend Formular Fields');
        $output->writeln('#####################');

        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'json')) {
                continue;
            }

            $components = [];

            foreach (DataHandler::getJsonDecode(DataHandler::getFile($file->getRealPath()), true) as $component) {
                $bundle = '';
                $nameSpace = '';
                
                foreach (DataHandler::explodeArray('/', $file->getPath()) as $partKey => $part) {
                    if (DataHandler::isStringInString($part, 'Bundle') && $part !== 'bundle') {
                        $bundle = $part;
                        $nameSpace = DataHandler::explodeArray('/', $file->getPath())[$partKey - 1];
                        break;
                    }
                }

                $componentFinder = new Finder();
                $componentFinder->files()->in($this->kernel->getProjectDir() . "/bundle/" . $nameSpace . '/' . $bundle)->name([$component['name'] . '.vue']);
                $componentFilePath = '';
                
                foreach ($componentFinder as $componentFile) {
                    $componentFilePath = $componentFile->getRelativePath();
                }

                $component['path'] = $nameSpace . '/' . $bundle . '/' . $componentFilePath . '/' . $component['name'];

                $components[] = $component;
            }

            $assets = DataHandler::mergeArray(
                $assets,
                $components
            );
        }

        $output->writeln('Done');
        $output->writeln('Writing Frontend Formular Fields');
        $output->writeln('#####################');

        DataHandler::writeFile($this->kernel->getProjectDir() . '/bundle/activeFormFields.json', DataHandler::getJsonEncode($assets));

        $assets = [];
        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . "/bundle")->name(['fields.json']);

        $output->writeln('Done');
        $output->writeln('Collecting Administration Assets');
        $output->writeln('#####################');

        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'json')) {
                continue;
            }

            $components = [];

            foreach (DataHandler::getJsonDecode(DataHandler::getFile($file->getRealPath()), true) as $component) {
                $bundle = '';
                $nameSpace = '';
                
                foreach (DataHandler::explodeArray('/', $file->getPath()) as $partKey => $part) {
                    if (DataHandler::isStringInString($part, 'Bundle') && $part !== 'bundle') {
                        $bundle = $part;
                        $nameSpace = DataHandler::explodeArray('/', $file->getPath())[$partKey - 1];
                        break;
                    }
                }

                $componentFinder = new Finder();
                $componentFinder->files()->in($this->kernel->getProjectDir() . "/bundle/" . $nameSpace . '/' . $bundle)->name([$component['name'] . '.vue']);
                $componentFilePath = '';
                
                foreach ($componentFinder as $componentFile) {
                    $componentFilePath = $componentFile->getRelativePath();
                }

                $component['path'] = $nameSpace . '/' . $bundle . '/' . $componentFilePath . '/' . $component['name'];

                $components[] = $component;
            }

            $assets = DataHandler::mergeArray(
                $assets,
                $components
            );
        }

        $output->writeln('Done');
        $output->writeln('Writing Administration Assets');
        $output->writeln('#####################');

        DataHandler::writeFile($this->kernel->getProjectDir() . '/bundle/activeFields.json', DataHandler::getJsonEncode($assets));

        $assets = [];
        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . "/bundle")->name(['dynamicFrontendComponents.json']);

        $output->writeln('Done');
        $output->writeln('Collecting Dynamic Frontend Components');
        $output->writeln('#####################');

        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'json')) {
                continue;
            }

            $components = [];

            foreach (DataHandler::getJsonDecode(DataHandler::getFile($file->getRealPath()), true) as $component) {
                $bundle = '';
                $nameSpace = '';
                
                foreach (DataHandler::explodeArray('/', $file->getPath()) as $partKey => $part) {
                    if (DataHandler::isStringInString($part, 'Bundle') && $part !== 'bundle') {
                        $bundle = $part;
                        $nameSpace = DataHandler::explodeArray('/', $file->getPath())[$partKey - 1];
                        break;
                    }
                }

                $componentFinder = new Finder();
                $componentFinder->files()->in($this->kernel->getProjectDir() . "/bundle/" . $nameSpace . '/' . $bundle)->name([$component['name'] . '.vue']);
                $componentFilePath = '';
                
                foreach ($componentFinder as $componentFile) {
                    $componentFilePath = $componentFile->getRelativePath();
                }

                if (DataHandler::isEmpty($component['path'] ?? '')) {
                    $component['path'] = $nameSpace . '/' . $bundle . '/' . $componentFilePath . '/' . $component['name'];
                }

                $components[] = $component;
            }

            $assets = DataHandler::mergeArray(
                $assets,
                $components
            );
        }

        $output->writeln('Done');
        $output->writeln('Writing Dynamic Frontend Components');
        $output->writeln('#####################');

        DataHandler::writeFile($this->kernel->getProjectDir() . '/bundle/activeComponents.json', DataHandler::getJsonEncode($assets));
        
        $output->writeln('Done');
        $output->writeln('Building Encore');
        $output->writeln('#####################');

        try {
            $process = new Process(['yarn', 'build']);
            $process->setTimeout(3600);
            $process->run();
            $process->waitUntil(function ($type, $output): bool {
                return $output === 'Ready. Waiting for commands...';
            });
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        } catch (ProcessFailedException $exception) {
            dd($process->getOutput());
        }
        
        $output->writeln('Done');
        $output->writeln('Clearing Cache');
        $output->writeln('#####################');

        /*$process = new Process(['php', 'bin/console', 'cache:clear']);
        $process->run();
        $process->waitUntil(function ($type, $output): bool {
            return $output === 'Ready. Waiting for commands...';
        });
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }*/
        $output->writeln('Done');

        $output->writeln('Complete: Threads were generated');
	    
        return 0;
    }

    private static function getStylePrefix(string $path): string
    {
        $pathArray = DataHandler::explodeArray('/', $path);
        
        return DataHandler::getLowerString($pathArray[0]) . '-' . DataHandler::getLowerString($pathArray[1]);
    }
}
