<?php

namespace Smug\Core\Context\Interface;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

interface ContextInterface
{
	public function buildFromRequest(Request $request, string $domain): void;

    public function getContainerVariable(string $variableName): ?object;

    public function getProjectDir(): string;

    public function getRequestData(): ?array;

	public function addRepository(string $key, string $entityName): void;

    public function getRepositories(): array;

    public function getConfig(): array;

    public function setConfig(array $config): void;

    public function getRepository($key): ?EntityRepository;
}
