<?php
namespace SiteApi\Controller;

use DvsaCommonApi\Controller\AbstractDvsaRestfulController;
use DvsaCommonApi\Model\ApiResponse;
use OrganisationApi\Service\OrganisationService;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;

class SiteOrganisationNameController extends AbstractDvsaRestfulController
{

    /**
     * @param OrganisationService $service
     */
    public function __construct(OrganisationService $service)
    {
        $this->service = $service;
    }

    public function get($id)
    {
        $data = $this->service->findOrganisationNameBySiteId($id);
        return ApiResponse::jsonOk($data);
    }
}
