<?php

namespace Smug\FrontendBundle\Controller\Frontend\Api\Newsletter;

use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Service\Newsletter\Contact\Add\AddService;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataController extends FeBaseController
{
    #[Route('/fe/api/newsletter/registration', name: 'fe_savfe_register_to_newslettere_diary', methods:"POST")]
    public function index(Request $request, AddService $service): JsonResponse
    {
        $data = DataHandler::getJsonDecode($request->getContent(), true);

        if ($data['fax'] !== '') {
            return $this->prepareReturn([
                'success' => false,
                'message' => 'Bot detected'
            ]);
        }

        return $this->prepareReturn($service->add($data));
    }
}