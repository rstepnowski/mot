<?php
/**
 * This file is part of the DVSA MOT Frontend project.
 *
 * @link http://gitlab.clb.npm/mot/mot
 */

namespace Event\Service;

use Core\Service\SessionService;
use Zend\Session\Container;

/**
 * Class EventSessionService.
 */
class EventSessionService extends SessionService
{
    const UNIQUE_KEY = 'event';
}
