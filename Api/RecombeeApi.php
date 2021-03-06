<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecombeeBundle\Api;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Templating\Helper\VersionHelper;
use Mautic\PageBundle\Model\TrackableModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Monolog\Logger;
use Recombee\RecommApi\Client;


class RecombeeApi extends AbstractRecombeeApi
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var VersionHelper
     */
    private $versionHelper;


    /**
     * TwilioApi constructor.
     *
     * @param TrackableModel    $pageTrackableModel
     * @param IntegrationHelper $integrationHelper
     * @param Logger            $logger
     * @param VersionHelper     $versionHelper
     *
     * @internal param CoreParametersHelper $coreParametersHelper
     */
    public function __construct(
        TrackableModel $pageTrackableModel,
        IntegrationHelper $integrationHelper,
        Logger $logger,
        VersionHelper $versionHelper
    ) {
        $this->logger = $logger;

        $integration = $integrationHelper->getIntegrationObject('Recombee');

        if ($integration && $integration->getIntegrationSettings()->getIsPublished()) {

            $keys = $integration->getDecryptedApiKeys();
            if (empty($keys)) {
                $keys['database'] = 'recombeemautic';
                $keys['secret_key'] = 'g1kyDDvmsJSzUYxKpDN0clpURJKjLanbRRoAwNGpNjsDkthD52i2rfsovYEr8nJD';
            }
            if (isset($keys['database']) && isset($keys['secret_key'])) {
                $this->client = new Client(
                    $keys['database'],
                    $keys['secret_key'],
                    'https',
                    ['serviceName' => 'Mautic '.$versionHelper->getVersion()]
                );
            }
        }
        parent::__construct($pageTrackableModel);
        $this->integrationHelper = $integrationHelper;
        $this->versionHelper = $versionHelper;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

}
