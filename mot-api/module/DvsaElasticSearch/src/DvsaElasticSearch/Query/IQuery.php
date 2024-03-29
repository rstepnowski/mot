<?php

namespace DvsaElasticSearch\Query;

/**
 * Interface IQuery
 *
 * All children od SuperSearchQuery MUST implement me to be functional.
 *
 * @package DvsaElasticSearch\Query
 */
interface IQuery
{
    public function execute($searchParams, $esConn = null, $esConfig = null);
}
