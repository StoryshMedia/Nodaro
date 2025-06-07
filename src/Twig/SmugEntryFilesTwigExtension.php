<?php declare(strict_types=1);

namespace Smug\Core\Twig;

use Smug\Core\Context\Context;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SmugEntryFilesTwigExtension extends AbstractExtension
{
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('smug_encore_entry_js_files', [$this, 'getWebpackJsFiles']),
            new TwigFunction('smug_encore_entry_css_files', [$this, 'getWebpackCssFiles']),
            new TwigFunction('smug_encore_entry_script_tags', [$this, 'renderWebpackScriptTags'], ['is_safe' => ['html']]),
            new TwigFunction('smug_encore_entry_link_tags', [$this, 'renderWebpackLinkTags'], ['is_safe' => ['html']]),
            new TwigFunction('smug_encore_entry_exists', [$this, 'entryExists']),
        ];
    }

    public function getWebpackJsFiles(string $entryName, string $entrypointName = '_default'): array
    {
        return $this->getEntrypointLookup($entrypointName)
            ->getJavaScriptFiles($entryName);
    }

    public function getWebpackCssFiles(string $entryName, string $entrypointName = '_default'): array
    {
        return $this->getEntrypointLookup($entrypointName)
            ->getCssFiles($entryName);
    }

    public function renderWebpackScriptTags(string $entryName, string $packageName = null, string $entrypointName = '_default', array $attributes = [], bool $async = false): string
    {
        if ($async === true) {
            $attributes['acync'] = true;
        }
        return $this->getTagRenderer()
            ->renderWebpackScriptTags($entryName, $packageName, $entrypointName, $attributes);
    }

    public function renderWebpackLinkTags(string $entryName, string $packageName = null, string $entrypointName = '_default', array $attributes = []): string
    {
        return $this->getTagRenderer()
            ->renderWebpackLinkTags($entryName, $packageName, $entrypointName, $attributes);
    }

    public function entryExists(string $entryName, string $entrypointName = '_default'): bool
    {
        $entrypointLookup = $this->getEntrypointLookup($entrypointName);
        if (!$entrypointLookup instanceof EntrypointLookup) {
            throw new \LogicException(sprintf('Cannot use entryExists() unless the entrypoint lookup is an instance of "%s"', EntrypointLookup::class));
        }

        return $entrypointLookup->entryExists($entryName);
    }

    private function getEntrypointLookup(string $entrypointName): EntrypointLookupInterface
    {
        return $this->context->getKernel()->getContainer()->get('webpack_encore.entrypoint_lookup_collection')
            ->getEntrypointLookup($entrypointName);
    }

    private function getTagRenderer(): TagRenderer
    {
        return $this->context->getKernel()->getContainer()->get('webpack_encore.tag_renderer');
    }
}
