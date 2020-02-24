<?php

namespace Orchestra\Facile\Template\Composers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Response;
use Orchestra\Support\Collection;
use Orchestra\Support\Contracts\Csvable;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

trait Csv
{
    /**
     * Compose CSV.
     */
    public function composeCsv(array $data = [], int $status = 200, array $config = []): SymfonyResponse
    {
        $filename = $config['filename'] ?? 'export';

        $collection = $this->convertToCsvable($data, $config);

        return Response::make($collection->toCsv(), $status, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
            'Cache-Control' => 'private',
            'pragma' => 'cache',
        ]);
    }

    /**
     * Stream CSV.
     */
    public function streamCsv(array $data = [], int $status = 200, array $config = []): SymfonyResponse
    {
        $filename = $config['filename'] ?? 'export';

        $collection = $this->convertToCsvable($data, $config);

        return Response::stream(static function () use ($collection) {
            $collection->streamCsv();
        }, $status, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
        ]);
    }

    /**
     * Convert content to CSV.
     */
    protected function convertToCsvable(array $data, array $config): Collection
    {
        $uses = $config['uses'] ?? 'data';
        $content = $data[$uses] ?? [];

        if (! $content instanceof Csvable) {
            if ($content instanceof Arrayable) {
                $content = $content->toArray();
            }

            $content = Collection::make(\array_map([$this, 'transformToArray'], $content));
        }

        return $content;
    }
}
