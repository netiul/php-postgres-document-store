<?php

declare(strict_types=1);

namespace EventEngine\DocumentStore\Postgres\OrderBy;

use EventEngine\DocumentStore;
use EventEngine\DocumentStore\OrderBy\OrderBy;

final class DefaultOrderByProcessor implements OrderByProcessor
{
    /**
     * @var bool
     */
    private $useMetadataColumns;

    public function __construct(bool $useMetadataColumns = false)
    {
        $this->useMetadataColumns = $useMetadataColumns;
    }

    public function process(OrderBy $orderBy): array
    {
        if($orderBy instanceof DocumentStore\OrderBy\AndOrder) {
            [$sortA, $sortAArgs] = $this->process($orderBy->a());
            [$sortB, $sortBArgs] = $this->process($orderBy->b());

            return ["$sortA, $sortB", \array_merge($sortAArgs, $sortBArgs)];
        }

        /** @var DocumentStore\OrderBy\Asc|DocumentStore\OrderBy\Desc $orderBy */
        $direction = $orderBy instanceof DocumentStore\OrderBy\Asc ? 'ASC' : 'DESC';
        $prop = $this->propToJsonPath($orderBy->prop());

        return ["{$prop} $direction", []];
    }

    private function propToJsonPath(string $field): string
    {
        if($this->useMetadataColumns && strpos($field, 'metadata.') === 0) {
            return str_replace('metadata.', '', $field);
        }

        return "doc->'" . str_replace('.', "'->'", $field) . "'";
    }
}
