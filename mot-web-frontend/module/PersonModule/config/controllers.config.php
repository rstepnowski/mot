<?php
/**
 * This file is part of the DVSA MOT Frontend project.
 *
 * @link http://gitlab.clb.npm/mot/mot
 */

use Dvsa\Mot\Frontend\PersonModule\Controller\ChangeAddressController;
use Dvsa\Mot\Frontend\PersonModule\Controller\ChangeNameController;
use Dvsa\Mot\Frontend\PersonModule\Controller\ChangeTelephoneController;
use Dvsa\Mot\Frontend\PersonModule\Controller\PersonProfileController;
use Dvsa\Mot\Frontend\PersonModule\Factory\Controller\ChangeAddressControllerFactory;
use Dvsa\Mot\Frontend\PersonModule\Factory\Controller\ChangeNameControllerFactory;
use Dvsa\Mot\Frontend\PersonModule\Factory\Controller\ChangeTelephoneControllerFactory;
use Dvsa\Mot\Frontend\PersonModule\Factory\Controller\PersonProfileControllerFactory;
use Dashboard\Controller\UserTradeRolesController;
use Dashboard\Factory\Controller\UserTradeRolesControllerFactory;
use UserAdmin\Factory\Controller\ChangeQualificationStatusControllerFactory;
use UserAdmin\Controller\ChangeQualificationStatusController;
use UserAdmin\Factory\Controller\UserProfileControllerFactory;
use UserAdmin\Controller\UserProfileController;
use Dvsa\Mot\Frontend\PersonModule\Controller\ChangeDateOfBirthController;
use Dvsa\Mot\Frontend\PersonModule\Factory\Controller\ChangeDateOfBirthControllerFactory;


return [
    'factories' => [
        UserProfileControllerFactory::class => UserProfileController::class,
        PersonProfileController::class    => PersonProfileControllerFactory::class,
        UserTradeRolesController::class   => UserTradeRolesControllerFactory::class,
        ChangeQualificationStatusController::class => ChangeQualificationStatusControllerFactory::class,
        ChangeAddressController::class => ChangeAddressControllerFactory::class,
        ChangeQualificationStatusController::class => ChangeQualificationStatusControllerFactory::class,
        ChangeNameController::class => ChangeNameControllerFactory::class,
        ChangeDateOfBirthController::class => ChangeDateOfBirthControllerFactory::class,
        ChangeTelephoneController::class => ChangeTelephoneControllerFactory::class
    ],
];