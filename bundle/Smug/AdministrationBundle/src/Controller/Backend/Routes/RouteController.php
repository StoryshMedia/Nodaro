<?php

namespace Smug\AdministrationBundle\Controller\Backend\Routes;

use Smug\AdministrationBundle\Trait\ViewDataProviderTrait;
use Smug\AdministrationBundle\Trait\ViewEventTrait;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Routing\Annotation\Route;

class RouteController extends BaseController
{
    use ViewDataProviderTrait;
    use ViewEventTrait;

    #[Route('/admin', name: 'admin')]
    public function index()
    {
        return $this->render('@SmugAdministration/backend/index/index.html.twig', []);
    }

    #[Route('/admin/login', name: 'admin_login')]
    public function loginPage()
    {
        return $this->render('@SmugAdministration/backend/index/login.html.twig', [
            'data' => [
                'image' => '/administration/img/login/login-screen-' . rand(1, 6) . '.webp'
            ]
        ]);
    }

    #[Route('/admin/{namespace}/{bundle}/{model}/{view}', name: 'admin_view')]
    #[Route('/admin/{namespace}/{bundle}/{model}/{view}/{id}', name: 'admin_detail_view')]
    #[Route('/admin/{namespace}/{bundle}/{model}/{view}/{parameters}', name: 'admin_parameter_view')]
    public function listPage(string $namespace, string $bundle, string $model, string $view, ?string $id = null, ?string $parameters = null)
    {
        $provider = $this->getViewProviderClass();
        $constantsClass = $this->getConstantsClass($namespace, $bundle, $model);
        $providerFunction = $this->getProviderFunction($view);

        if (DataHandler::isEmpty($providerFunction)) {
            return $this->redirectToRoute('error', []);
        }

        $data = $provider::$providerFunction($constantsClass, $this->em, $this->context);

        if (DataHandler::isEmpty($data)) {
            return $this->redirectToRoute('error', []);
        }

        if (!DataHandler::isEmpty($id)) {
            $data->addConfigItem('id', $id);
            $data = self::bypassIdToConfigFields($id, $data);
        }

        $data = $this->dispatchViewEvent($data, $constantsClass, $view);

        return $this->render('@SmugAdministration/backend/' . $view . '.html.twig', ['view' => $data->toArray()]);
    }
}