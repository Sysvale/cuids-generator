<?php

namespace Sysvale\CuidsGenerator\Console\Editors;

use Sysvale\CuidsGenerator\Support\FieldType;
use Illuminate\Console\Command;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\table;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\confirm;

class FieldEditor
{
    public function run(Command $cli): array
    {
        $fields = [];

        info('Configuração dos campos');

        while (true) {
            $action = select(
                label: 'O que deseja fazer com os campos?',
                options: [
                    'add' => 'Adicionar',
                    'rename' => 'Atualizar nome',
                    'change-type' => 'Atualizar tipo',
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
                'remove' => $this->remove($fields),
                'list' => $this->list($fields),
                'done' => null,
            };

            if ($action === 'done') break;
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

        $type = select(
            label: "Qual o tipo de '{$name}'?",
            options: FieldType::values(),
            default: FieldType::values()[0]
        );

        $fields[$name] = $type;
    }

private function rename(array &$fields): void
    {
        if ($this->isEmpty($fields)) return;

        $old = select('Selecione o campo para renomear', array_keys($fields));
        $new = text(
            label: "Novo nome para '{$old}'",
            validate: fn (string $value) => match (true) {
                empty($value) => 'O nome é obrigatório.',
                isset($fields[$value]) => 'Este nome já está em uso.',
                default => null,
            }
        );

        $fields[$new] = $fields[$old];
        unset($fields[$old]);
    }

    private function changeType(array &$fields): void
    {
        if ($this->isEmpty($fields)) return;

        $name = select('Alterar tipo de qual campo?', array_keys($fields));
        $fields[$name] = select(
            label: "Novo tipo para '{$name}'",
            options: FieldType::values()
        );
    }

    private function remove(array &$fields): void
    {
        if ($this->isEmpty($fields)) return;

        $name = select('Remover qual campo?', array_keys($fields));
        
        if (confirm("Tem certeza que deseja remover '{$name}'?")) {
            unset($fields[$name]);
        }
    }

    private function list(array $fields): void
    {
        if ($this->isEmpty($fields)) return;

        table(
            ['Campo', 'Tipo'],
            collect($fields)->map(fn ($t, $n) => [$n, $t])->toArray()
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
}
