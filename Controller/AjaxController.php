<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecombeeBundle\Controller;

use Guzzle\Http\Message\Response;
use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use MauticPlugin\MauticRecombeeBundle\Entity\Recombee;
use MauticPlugin\MauticRecombeeBundle\Helper\RecombeeHelper;
use MauticPlugin\MauticRecombeeBundle\Model\RecombeeModel;
use MauticPlugin\MauticRecombeeBundle\Service\RecombeeGenerator;
use MauticPlugin\MauticRecombeeBundle\Service\RecombeeToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Recombee\RecommApi\Requests as Reqs;
use Recombee\RecommApi\Exceptions as Ex;

class AjaxController extends CommonAjaxController
{

    /**
     * {@inheritdoc}
     */
    protected function getModelName()
    {
        return 'recombee.recombee';
    }

    public function getAction()
    {
        /** @var LeadModel $model */
        $model = $this->getModel('lead');
        /** @var DynamicContentHelper $helper */
        $helper = $this->get('mautic.helper.dynamicContent');
        /** @var DeviceTrackingServiceInterface $deviceTrackingService */
        $deviceTrackingService = $this->get('mautic.lead.service.device_tracking_service');
        /** @var PageModel $pageModel */
        $pageModel = $this->getModel('page');

        $request = $this->get('request_stack');

        /** @var Lead $lead */
        $lead    = $model->getContactFromRequest($pageModel->getHitQuery($this->request));


        /** @var RecombeeGenerator $recombeeGenerator */
        $recombeeGenerator = $this->get('mautic.recombee.service.token.generator');

        /** @var RecombeeToken $recombeeToken */
        $recombeeToken = $this->get('mautic.recombee.service.token');
        $recombeeToken->setToken($this->request->query->all());
        $content       = $recombeeGenerator->getContentByToken($recombeeToken, 'pageTemplate');
        $trackedDevice = $deviceTrackingService->getTrackedDevice();
        $deviceId      = ($trackedDevice === null ? null : $trackedDevice->getTrackingId());

        return empty($content)
            ? new Response('', Response::HTTP_NO_CONTENT)
            : new JsonResponse(
                [
                    'content'   => $content,
                    'id'        => $lead->getId(),
                    'sid'       => $deviceId,
                    'device_id' => $deviceId,
                ]
            );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function generateExampleAction(Request $request)
    {
        /** @var RecombeeHelper $recombeeHelper */
        $recombeeHelper = $this->get('mautic.recombee.helper');
        /** @var RecombeeModel $recombeeModel */
        $recombeeModel = $this->getModel($this->getModelName());
        $recombeeId = $request->request->get('recombeeId');
        $recombeeId = 1;
        $enity = $recombeeModel->getEntity($recombeeId);

        $data = [];
        $success = false;
        $error = '';
        $content = '';
        $items = [];

        if($enity && $enity->isPublished(true)){
            $class = $enity->getType();
            try {
                $items =     $recombeeHelper->getClient()->send(new $class(2, 9,    [
                    'returnProperties' => true,
                    'includedProperties' => ['name']
                ] ));
                $success = true;
            } catch (Ex\ApiException $e) {
                $error =  $e->getMessage();
            }
        }



        $data['items'] = $items;
        $data['error'] = $error;
        $data['success'] = $success;

        return $this->sendJsonResponse($data);
    }
}
