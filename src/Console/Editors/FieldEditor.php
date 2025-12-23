<?php

namespace Sysvale\CuidsGenerator\Console\Editors;

use Illuminate\Support\Str;
use Sysvale\CuidsGenerator\Support\FieldType;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\table;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\confirm;

class FieldEditor
{
    public function run(): array
    {
        $fields = [];

        info('Defina os atributos do model');

        while (true) {
            $action = select(
                label: 'Gerencie os atributos do model',
                options: [
                    'add' => 'Adicionar',
                    'rename' => 'Atualizar nome',
                    'change-type' => 'Atualizar tipo',
                    'change-required' => 'Alterar obrigatoriedade',
                    'remove' => 'Remover',
                    'list' => 'Listar',
                    'done' => 'Ir para próxima etapa',
                ],
                default: 'add',
            );

            match ($action) {
                'add' => $this->add($fields),
                'rename' => $this->rename($fields),
                'change-type' => $this->changeType($fields),
                'change-required' => $this->changeRequired($fields),
                'remove' => $this->remove($fields),
                'list' => $this->list($fields),
                'done' => null,
            };

            if ($action === 'done') {
                break;
            }
        }

        return $fields;
    }

    private function add(array &$fields): void
    {
        $name = text(
            label: 'Nome do campo',
            placeholder: 'ex: title',
            validate: fn (string $value) => match (true) {
                empty($value) => 'O nome é obrigatório',
                isset($fields[$value]) => 'Este campo já existe',
                default => null,
            }
        );

        $fieldName = $this->sanitizeFieldName($name);

        $type = select(
            label: "Qual o tipo de '{$fieldName}'?",
            options: FieldType::values(),
            default: FieldType::values()[0]
        );

        $requiredChoice = select(
            label: "O campo '{$fieldName}' é obrigatório?",
            options: ['Sim', 'Não'],
            default: 'Sim'
        );

        $fields[$fieldName] = [
            'name' => $fieldName,
            'type' => $type,
            'required' => $requiredChoice === 'Sim',
            'nullable' => $requiredChoice === 'Não',
        ];

        $this->list($fields);
    }

    private function rename(array &$fields): void
    {
        if ($this->isEmpty($fields)) {
            return;
        }

        $old = select('Selecione o campo para renomear', array_keys($fields));
        $new = text(
            label: "Novo nome para '{$old}'",
            validate: fn (string $value) => match (true) {
                empty($value) => 'O nome é obrigatório.',
                isset($fields[$value]) => 'Este nome já está em uso.',
                default => null,
            }
        );

        $fieldName = $this->sanitizeFieldName($new);

        $fields[$fieldName] = $fields[$old];
        $fields[$fieldName]['name'] = $fieldName;
        unset($fields[$old]);

        $this->list($fields);
    }

    private function changeType(array &$fields): void
    {
        if ($this->isEmpty($fields)) {
            return;
        }

        $name = select('Alterar tipo de qual campo?', array_keys($fields));

        $newType = select(
            label: "Novo tipo para '{$name}'",
            options: FieldType::values()
        );

        $fields[$name]['type'] = $newType;

        $this->list($fields);
    }

    private function changeRequired(array &$fields): void
    {
        if ($this->isEmpty($fields)) {
            return;
        }

        $name = select('Alterar obrigatoriedade de qual campo?', array_keys($fields));

        $requiredChoice = select(
            label: "Novo status de obrigatoriedade para '{$name}'",
            options: ['Sim', 'Não'],
            default: $fields[$name]['required'] ? 'Sim' : 'Não'
        );

        $fields[$name]['required'] = $requiredChoice === 'Sim';
        $fields[$name]['nullable'] = $requiredChoice === 'Não';

        $this->list($fields);
    }

    private function remove(array &$fields): void
    {
        if ($this->isEmpty($fields)) {
            return;
        }

        $name = select('Remover qual campo?', array_keys($fields));

        $removeField = confirm(
            label: "Tem certeza que deseja remover '{$name}'?",
            default: true,
            yes: 'Sim',
            no: 'Não'
        );

        if (!$removeField) {
            return;
        }

        unset($fields[$name]);

        $this->list($fields);
    }

    private function list(array $fields): void
    {
        if ($this->isEmpty($fields)) {
            return;
        }

        table(
            ['Campo', 'Tipo', 'Obrigatório'],
            collect($fields)->map(function ($field) {
                return [
                    $field['name'],
                    $field['type'],
                    $field['required'] ? '✓ Sim' : '✗ Não'
                ];
            })->toArray()
        );
    }

    private function isEmpty(array $fields): bool
    {
        if (empty($fields)) {
            warning('Nenhum campo configurado até o momento.');
            return true;
        }
        return false;
    }

    private function sanitizeFieldName(string $name): string
    {
        return Str::snake(Str::camel(Str::singular($name)));
    }
}
