<?php

namespace Smug\Core\Context;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use Smug\Core\Context\Interface\ContextInterface;
use Smug\Core\Context\Traits\GetPreparerTrait;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Exception\Base\NotAllowedException;
use Smug\Core\Security\SecurityProvider;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\SystemBundle\Entity\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class Context implements ContextInterface
{
    use GetPreparerTrait;

    private $em;
    private $container;
    private $user = null;
    private $identifier = null;
    private $identifierKey = null;
    private $parent = null;
    private $preparer = '';
    private $mode = 'be';
    private $repositories = [];
    private $config = [];
    private $projectDir;
    private $kernel;
    private $requestData;
    private $request;

    public function __construct(
        KernelInterface $kernel,
        EntityManagerInterface $em
    )
    {
        $this->container = $kernel->getContainer();
        $this->projectDir = $kernel->getProjectDir();
        $this->kernel = $kernel;
        $this->em = $em;
        if ($this->getContainerVariable('security.token_storage')->getToken()) {
            $this->user = $this->getContainerVariable('security.token_storage')->getToken()->getUser();  
        }

        if (DataHandler::doesKeyExists('REQUEST_URI', $_SERVER)) {
            $this->mode = (DataHandler::isStringInString($_SERVER['REQUEST_URI'], '/fe/api')) ? 'fe' : 'be';
        } else {
            $this->mode = 'cli';
        }
    }

    public function buildFromRequest(Request $request, string $domain = '', array $config = []): void
    {
        $this->requestData = DataHandler::getJsonDecode($request->getContent(), true);
        $this->request = $request;
        $this->config = $config;

        if (!DataHandler::isEmpty($domain)) {
            $this->repositories['main'] = $domain;
            $this->preparer = $this->getPreparer($domain);
        }
    }

    public function buildFromData(array $data, string $domain, array $config = []): void
    {
        $this->requestData = $data;
        $this->config = $config;
        $this->repositories['main'] = $domain;
        $this->preparer = $this->getPreparer($domain);
    }

    public function getPublicDir(): string
    {
        return $this->getKernel()->getProjectDir() . DIRECTORY_SEPARATOR . 'public';
    }

    public function getConfigDir(): string
    {
        return $this->getKernel()->getProjectDir() . DIRECTORY_SEPARATOR . 'config';
    }

    public function updateData(string $field, mixed $value): void
    {
        $this->requestData[$field] = $value;
    }

    public function getContainerVariable(string $variableName): ?object 
    {
        return $this->container->get($variableName);
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }

    public function getProjectDir(): string 
    {
        return $this->projectDir;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function getRequestData(): ?array
    {
        return $this->requestData;
    }

    public function setRequestData(array $data): void
    {
        $this->requestData = $data;
    }

    public function getUser(): ?UserInterface
    {
        if (!$this->user) {
            return null;
        }

        $userClass = User::class;

        if ($this->mode === 'fe' && DataHandler::doesClassExist(\Smug\FrontendUserBundle\Entity\FrontendUser\FrontendUser::class)) {
            $userClass = \Smug\FrontendUserBundle\Entity\FrontendUser\FrontendUser::class;
        }

        return $this->em->getRepository(EntityGenerator::getGeneratedEntity($userClass))->findOneBy(['email' => $this->user->__get('email')]);
    }

    public function getUserArray(): ?array
    {
        if (DataHandler::isEmpty($this->user)) {
            return null;
        }

        return $this->user->toArray();
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setParentModel(BaseModel $parent): void
    {
        $this->parent = $parent;
    }

    public function getParentModel(): BaseModel
    {
        return $this->parent;
    }

    public function setIdentifier(string|int $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function setIdentifierKey(string $identifierKey): void
    {
        $this->identifierKey = $identifierKey;
    }

    public function getIdentifier(): string|int|null
    {
        return $this->identifier;
    }

    public function getIdentifierKey(): string|int|null
    {
        return $this->identifierKey;
    }

    public function getConfigItem(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    public function setConfigItem(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    public function addRepository(string $key, string $entityName): void
    {
        $this->repositories[$key] = $entityName;
    }

    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function getRepository($key): ?EntityRepository
    {
        if (!DataHandler::doesKeyExists($key, $this->repositories)) {
            return null;
        }

        return $this->em->getRepository($this->repositories[$key]);
    }

    public function getMainRepository(): ?EntityRepository
    {
        if (DataHandler::isStringInString($this->repositories['main'], 'Entity\\Generated')) {
            return $this->em->getRepository($this->repositories['main']);
        }

        return $this->em->getRepository(EntityGenerator::getGeneratedEntity($this->repositories['main']));
    }

    public function getRepositoryClass($key): ?string
    {
        if (!DataHandler::doesKeyExists($key, $this->repositories)) {
            return null;
        }

        return $this->repositories[$key];
    }

    public function getMainRepositoryClass(): ?string
    {
        return $this->repositories['main'];
    }                                                                                                      

    public function getMainEntity(): ?BaseModel
    {
        return $this->em->getRepository($this->repositories['main'])->findOneBy(['id' => $this->getRequestData()['id']]);
    }

    public function getEntityByIdentifier($identifier, $field = 'id', $repository = 'main'): ?BaseModel
    {
        if (!DataHandler::doesKeyExists($repository, $this->repositories)) {
            $this->addRepository($repository, $repository);
        }

        return $this->em->getRepository($this->repositories[$repository])->findOneBy([$field => $identifier]);
    }

    public function getEntitiesByIdentifier($identifier, $field = 'id', $repository = 'main'): array
    {
        if (!DataHandler::doesKeyExists($repository, $this->repositories)) {
            $this->addRepository($repository, $repository);
        }

        return $this->em->getRepository($this->repositories[$repository])->findBy([$field => $identifier]);
    }

    public function getAllEntities($repository = 'main'): ?array
    {
        return $this->em->getRepository($this->repositories[$repository])->findAll();
    }

    public function getEntityByMultiple(array $criteria, $repository = 'main'): ?BaseModel
    {
        return $this->em->getRepository($this->repositories[$repository])->findOneBy($criteria);
    }

    public function getByIdentifier($identifier, $field = 'id', $repository = 'main'): array
    {
        return $this->em->getRepository($this->repositories[$repository])->findBy([$field => $identifier]);
    }

    public function getByRestrictions(array $restrictions, $repository = 'main'): array
    {
        $count = 0;
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->select('c')
            ->from($this->repositories[$repository] ?? 'main', 'c');

        foreach ($restrictions as $restriction) {
            if ($count === 0) {
                $queryBuilder
                ->where('c.' . $restriction['condition'] . ' = :' . $restriction['condition'])
                ->setParameter($restriction['condition'], $restriction['value']);
            } else {
                $queryBuilder
                ->andWhere('c.' . $restriction['condition'] . ' = :' . $restriction['condition'])
                ->setParameter($restriction['condition'], $restriction['value']);
            }

            $count++;
        }


        return $queryBuilder->getQuery()->getResult();
    }

    public function getPreparerInstance(): ?object
    {
        if (DataHandler::isEmpty($this->preparer)) {
            return null;
        }

        try {
            return ServiceGenerationFactory::createInstance($this->preparer);
        } catch (Exception $ex) {
            return null;
        }
    }
    
    public function isAllowed(string $right, string $class): bool
    {
        if ($this->user->__get('userGroup')->__get('admin') === true) {
            return true;
        }

        /** @var User $user */
        $user = $this->getUser();

        if (!DataHandler::isInstanceOf($user, EntityGenerator::getGeneratedEntity(User::class))) {
            return false;
        }

        $class = DataHandler::getFirstCapitalUpper(DataHandler::getCamelCaseString($class));
        $permission = SecurityProvider::getModelPermissions($class, $user->__get('userGroup'));

        if (DataHandler::isEmpty($permission)) {
            throw new NotAllowedException('Action not allowed with given permissions');
        }

        return ($permission->__get('can' . DataHandler::getFirstCapitalUpper($right)) === true);
    }
    
    public function getSpecialFields(string $class, string $type): array
    {
        if (DataHandler::isEmpty($this->user)) {
            return [];
        }
        if ($this->user->__get('userGroup')->__get('admin') === true) {
            return [];
        }

        /** @var User $user */
        $user = $this->getUser();

        if (!DataHandler::isInstanceOf($user, User::class)) {
            return [];
        }

        $permission = SecurityProvider::getModelPermissions($class, $user->__get('userGroup'));

        return DataHandler::explodeArray(',', $permission->__get($type));
    }
}