<?php

namespace Smug\FrontendBundle\Controller\Frontend\Api\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class BackupController extends FeBaseController
{
    #[Route('/fe/api/json/backup/download/{name}', name: 'fe_download_backup', methods:"POST")]
    public function downloadBackupAction(string $name, Request $request): Response
    {
        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $name);

        $response->setContent($request->getContent());

        return $response;
    }
}