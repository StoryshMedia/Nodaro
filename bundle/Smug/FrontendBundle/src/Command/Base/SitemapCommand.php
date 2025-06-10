<?php

namespace Smug\FrontendBundle\Command\Base;

use Smug\BlogBundle\Entity\Blog\BlogEntry;
use Smug\CommunityBundle\Entity\Thread\Thread;
use Smug\EventBundle\Entity\Event\Event;
use Smug\FrontendUserBundle\Entity\FrontendUser\FrontendUser;
use Smug\MarketBundle\Entity\Market\Market;
use Smug\StoryBundle\Entity\Story\Story;
use Smug\Core\Service\Base\Components\Generator\XmlGenerator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Factory\SolrClientFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\HttpKernel\KernelInterface;

class SitemapCommand extends Command
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->container = $kernel->getContainer();
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('create:sitemap')
            ->setDescription('Creating sitemap.xml.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ServiceGenerationFactory::init($this->container);

        $sitemapUrls = [];
        $sitemapUrls = DataHandler::mergeArray(
            $sitemapUrls,
            $this->generateSitemapFromSolr(
                'author',
                'authors'
            )
        );

        $sitemapUrls = DataHandler::mergeArray(
            $sitemapUrls,
            $this->generateSitemapFromSolr(
                'publication',
                'publications'
            )
        );
        $sitemapUrls = DataHandler::mergeArray(
            $sitemapUrls,
            $this->generateSitemap(
                Story::class,
                'story',
                'stories',
                $output,
                ['approved' => true]
            )
        );
        $sitemapUrls = DataHandler::mergeArray(
            $sitemapUrls,
            $this->generateSitemap(
                Market::class,
                'market',
                'tauschboerse',
                $output
            )
        );
        $sitemapUrls = DataHandler::mergeArray(
            $sitemapUrls,
            $this->generateSitemap(
                Thread::class,
                'thread',
                'community/forum',
                $output
            )
        );
        $sitemapUrls = DataHandler::mergeArray(
            $sitemapUrls,
            $this->generateSitemap(
                FrontendUser::class,
                'user',
                'community',
                $output,
                ['hidden' => false]
            )
        );
        $sitemapUrls = DataHandler::mergeArray(
            $sitemapUrls,
            $this->generateSitemap(
                BlogEntry::class,
                'blog',
                'blog',
                $output,
                ['hidden' => false]
            )
        );
        $sitemapUrls = DataHandler::mergeArray(
            $sitemapUrls,
            $this->generateSitemap(
                Event::class,
                'event',
                'events',
                $output,
                ['approved' => true]
            )
        );
        
        $sitemap = XmlGenerator::getSitemapIndexHeader();
        foreach ($sitemapUrls as $sitemapUrl) {
            $sitemap .= XmlGenerator::getSitemapIndexUrl($sitemapUrl);
        }
        $sitemap .= XmlGenerator::getSitemapIndexFooter();
        DataHandler::writeFile(__DIR__ . '/sitemap/sitemap_index.xml', $sitemap);

        return 0;
    }

    private function generateSitemapFromSolr(
        string $mode,
        string $baseSlug
    ): array
    {
        $client = SolrClientFactory::getSolrClient($baseSlug);

        $further = true;
        $start = 1;
        $sitemapCount = 1;
        $sitemapUrls = [];

        while ($further === true) {
            $query = $client->createSelect(array(
                'fields' => ['slug']
            ));
            $query->setStart($start);
            $query->setRows(10000);

            $start = $start + 10000;
            $resultset = $client->select($query);

            $sitemap = XmlGenerator::getSitemapHeader();
            foreach ($resultset as $document) {
                $slug = 'https://storysh.de/' . $baseSlug . '/' . $document['slug'];
                $sitemap .= XmlGenerator::getSitemapUrl($slug);
            }
            $sitemap .= XmlGenerator::getSitemapFooter();
            DataHandler::writeFile(__DIR__ . '/sitemap/' . $mode . 'Sitemap' . $sitemapCount . '.xml', $sitemap);

            $sitemapUrls[] = '/sitemaps/' . $mode . 'Sitemap' . $sitemapCount . '.xml';
            $sitemapCount++;

            if (count($resultset) < 10000) {
                $further = false;
                break;
            }
        }

        return $sitemapUrls;
    }

    private function generateSitemap(
        string $class,
        string $mode,
        string $baseSlug,
        OutputInterface $output,
        array $findBy = null
    ): array
    {
        $output->writeln('building ' . $mode . ' sitemap');

        if ($findBy === null) {
            $items = $this->em->getRepository($class)->findAll();
        } else {
            $items = $this->em->getRepository($class)->findBy($findBy);
        }

        $progressBar = new ProgressBar($output, count($items));
        $count = 1;
        $sitemapCount = 1;
        $sitemapUrls = [];
        $sitemap = XmlGenerator::getSitemapHeader();
        foreach ($items as $item) {
            $slug = 'https://storysh.de/' . $baseSlug . '/' . $item->getSlug();
            $sitemap .= XmlGenerator::getSitemapUrl($slug);

            if ($count === 20000) {
                $sitemap .= XmlGenerator::getSitemapFooter();
                DataHandler::writeFile(__DIR__ . '/sitemap/' . $mode . 'Sitemap' . $sitemapCount . '.xml', $sitemap);
                $sitemapUrls[] = '/sitemaps/' . $mode . 'Sitemap' . $sitemapCount . '.xml';
                $count = 1;
                $sitemapCount++;
                $sitemap = XmlGenerator::getSitemapHeader();
            }
            $count++;
            $progressBar->advance();
        }
        $sitemap .= XmlGenerator::getSitemapFooter();
        DataHandler::writeFile(__DIR__ . '/sitemap/' . $mode . 'Sitemap' . $sitemapCount . '.xml', $sitemap);
        if (DataHandler::getArrayLength($items) > 0) {
            $sitemapUrls[] = '/sitemaps/' . $mode . 'Sitemap' . $sitemapCount . '.xml';
        }
        $progressBar->finish();

        return $sitemapUrls;
    }
}
