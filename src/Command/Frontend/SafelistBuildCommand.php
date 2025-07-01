<?php

namespace Smug\Core\Command\Frontend;

use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;
use Smug\FrontendBundle\Entity\ContentItemModuleField\ContentItemModuleField;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SafelistBuildCommand extends Command
{
    public function __construct(protected Context $context)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('frontend:safelist:build')
            ->setDescription('builds the safelist for used tailwind classes in content elements');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $classes = [];

        $output->writeln('Getting content elements');
        $output->writeln('#####################');

        $contentElements = $this->context->getEntityManager()->getRepository(EntityGenerator::getGeneratedEntity(ContentItem::class))->findAll();
        
        $output->writeln('Done');
        $output->writeln('Collecting used tailwind classes');
        $output->writeln('#####################');

        foreach ($contentElements as $element) {
            $elementClasses = DataHandler::getJsonDecode(
                $element->__get('additionalClasses'),
                true
            );

            foreach ($elementClasses as $elementClass) {
                if (!DataHandler::isInArray($elementClass, $classes)) {
                    $classes[] = $elementClass;
                }
            }
        }

        $contentElementFields = $this->context->getEntityManager()->getRepository(EntityGenerator::getGeneratedEntity(ContentItemModuleField::class))->findAll();

        foreach ($contentElementFields as $field) {
            foreach ($field->__get('classes') as $fieldClass) {
                if (!DataHandler::isInArray($fieldClass, $classes)) {
                    $classes[] = $fieldClass;
                }
            }
        }

        $output->writeln('Done');
        $output->writeln('Writing Safelist File');
        $output->writeln('#####################');

        DataHandler::writeFile(__DIR__ . '/safelist.json', DataHandler::getJsonEncode($classes));

        $output->writeln('Done');
        $output->writeln('#####################');
        $output->writeln('Complete: Safelist generated');
	    
        return 0;
    }
}
