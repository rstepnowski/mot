<?php

namespace DvsaMotApi\Service\RoleRefresher;

/**
 * Interface RoleRefresherInterface
 *
 * @package DvsaMotApi\Service\RoleRefresher
 */
interface RoleRefresherInterface
{

    /**
     * @param $userId
     *
     * @return bool true if refresh operation changed any roles
     */
    public function refresh($userId);
}
