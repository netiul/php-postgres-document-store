<?php

declare(strict_types=1);

namespace EventEngine\DocumentStore\Postgres\OrderBy;

use EventEngine\DocumentStore\OrderBy\OrderBy;

interface OrderByProcessor
{
    public function process(OrderBy $orderBy): array;
}
