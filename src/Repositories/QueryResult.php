<?php

namespace The7055inc\Shared\Repositories;

/**
 * Class QueryResult
 * @package The7055inc\Shared\Repositories
 */
class QueryResult
{
    /**
     * List of items
     * @var array
     */
    public $items = array();
    /**
     * Total item count
     * @var int
     */
    public $totalCount = 0;

    /**
     * Current page item count
     * @var int
     */
    public $count = 0;
}