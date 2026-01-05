<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FormFieldBuilder
{
    public function handle(string $model, array $fields): void
    {
        $lowerModel = Str::lower(Str::plural($model));

        $directory = resource_path("js/features/{$lowerModel}/constants");

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $fileName = Str::kebab($model) . "FormFields.ts";

        $path = "{$directory}/{$fileName}";

        $mappedFields = collect($fields)->map(function ($field) {
            return $this->mapToFrontendStructure($field);
        })->values()->toArray();

        $jsonFields = json_encode($mappedFields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $jsObject = preg_replace('/"([^"]+)":/', '$1:', $jsonFields);

        $content = "export default {$jsObject};";

        File::put($path, $content);
    }

    private function mapToFrontendStructure(array $field): array
    {
        $name = Str::kebab($field['name']);
        $label = Str::headline($name);

        return [
            'name' => $name,
            'label' => $label,
            'validateLabel' => $label,
            'component' => $this->mapComponent($field['type']),
            'fluid' => true,
            'required' => $field['required'],
            'rules'  => $field['required'] ? 'required' : '',
            'colSpan' => 12
        ];
    }

    private function mapComponent(string $type): string
    {
        return match ($type) {
            'date', 'datetime' => 'CdsDateInput',
            'boolean'  => 'CdsCheckbox',
            'integer', 'float' => 'CdsNumberInput',
            default  => 'CdsTextInput',
        };
    }
}
