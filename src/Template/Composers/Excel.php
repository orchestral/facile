<?php

namespace Orchestra\Facile\Template\Composers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel as Exporter;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

trait Excel
{
    /**
     * Compose XLS.
     */
    public function composeXls(array $data = [], int $status = 200, array $config = []): SymfonyResponse
    {
        return $this->composeExcel($data, $config, Exporter::XLS);
    }

    /**
     * Compose XLSX.
     */
    public function composeXlsx(array $data = [], int $status = 200, array $config = []): SymfonyResponse
    {
        return $this->composeExcel($data, $config, Exporter::XLSX);
    }

    /**
     * Compose excel.
     */
    protected function composeExcel(array $data, array $config, string $writerType): SymfonyResponse
    {
        $filename = $config['filename'] ?? 'export';

        return $this->convertToExcel($data, $config)->exportExcel($filename, $writerType);
    }

    /**
     * Convert content to CSV.
     *
     * @return \Orchestra\Support\Collection
     */
    protected function convertToExcel(array $data, array $config): Collection
    {
        $uses = $config['uses'] ?? 'data';
        $content = $data[$uses] ?? [];

        if ($content instanceof Arrayable) {
            $content = $content->toArray();
        }

        return Collection::make(\array_map([$this, 'transformToArray'], $content));
    }
}
