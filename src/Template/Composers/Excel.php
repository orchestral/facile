<?php

namespace Orchestra\Facile\Template\Composers;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel as Exporter;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

trait Excel
{
    /**
     * Compose XLS.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $config
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function composeXls(array $data = [], int $status = 200, array $config = []): SymfonyResponse
    {
        return $this->composeExcel($data, $config, Exporter::XLS);
    }

    /**
     * Compose XLSX.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $config
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function composeXlsx(array $data = [], int $status = 200, array $config = []): SymfonyResponse
    {
        return $this->composeExcel($data, $config, Exporter::XLSX);
    }

    /**
     * Compose excel.
     *
     * @param  array  $data
     * @param  array  $config
     * @param  string  $writerType
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function composeExcel(array $data, array $config, string $writerType): SymfonyResponse
    {
        $filename = $config['filename'] ?? 'export';

        return $this->convertToExcel($data, $config)->exportExcel($filename, $writerType);
    }

    /**
     * Convert content to CSV.
     *
     * @param  array  $data
     * @param  array  $config
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
