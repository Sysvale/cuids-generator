<?php

namespace Sysvale\CuidsGenerator\Console\Editors;

use Sysvale\CuidsGenerator\Support\FieldType;
use Illuminate\Console\Command;

class FieldEditor
{
    public function run(Command $cli): array
    {
        $fields = [];

        $cli->info('Configuração dos campos');

        while (true) {
            $action = $cli->choice(
                'Campos',
                ['Adicionar', 'Atualizar nome', 'Atualizar tipo', 'Remover', 'Listar', 'Finalizar'],
            );

            match ($action) {
                'Adicionar' => $this->add($cli, $fields),
                'Atualizar nome' => $this->rename($cli, $fields),
                'Atualizar tipo' => $this->changeType($cli, $fields),
                'Remover' => $this->remove($cli, $fields),
                'Listar' => $this->list($cli, $fields),
                'Finalizar' => null,
            };

            if ($action === 'Finalizar') break;
        }

        return $fields;
    }

    private function add(Command $cli, array &$fields): void
    {
        $name = $cli->ask('Nome do campo');

        if (! $name || isset($fields[$name])) {
            $cli->warn('Campo inválido ou duplicado.');
            return;
        }

        $type = $cli->choice('Tipo', FieldType::values(), 0);

        $fields[$name] = $type;
    }

    private function rename(Command $cli, array &$fields): void
    {
        if (empty($fields)) {
            $cli->warn('Nenhum campo.');
            return;
        }

        $old = $cli->choice('Campo', array_keys($fields));
        $new = $cli->ask('Novo nome');

        if (! $new || isset($fields[$new])) {
            $cli->warn('Nome inválido.');
            return;
        }

        $fields[$new] = $fields[$old];
        unset($fields[$old]);
    }

    private function changeType(Command $cli, array &$fields): void
    {
        if (empty($fields)) return;

        $name = $cli->choice('Campo', array_keys($fields));
        $fields[$name] = $cli->choice('Tipo', FieldType::values(), 0);
    }

    private function remove(Command $cli, array &$fields): void
    {
        if (empty($fields)) return;

        $name = $cli->choice('Campo', array_keys($fields));
        unset($fields[$name]);
    }

    private function list(Command $cli, array $fields): void
    {
        $cli->table(['Campo', 'Tipo'], collect($fields)->map(fn ($t, $n) => [$n, $t]));
    }
}
