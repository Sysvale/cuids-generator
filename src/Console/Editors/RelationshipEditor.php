<?php

namespace Sysvale\CuidsGenerator\Console\Editors;

use Sysvale\CuidsGenerator\Support\RelationshipType;
use Illuminate\Console\Command;

class RelationshipEditor
{
    public function run(Command $cli): array
    {
        $relationships = [];

        $cli->info('Configuração de relacionamentos');

        while (true) {
            $action = $cli->choice(
                'Relacionamentos',
                ['Adicionar', 'Atualizar', 'Remover', 'Listar', 'Finalizar'],
            );

            match ($action) {
                'Adicionar' => $this->add($cli, $relationships),
                'Atualizar' => $this->update($cli, $relationships),
                'Remover' => $this->remove($cli, $relationships),
                'Listar' => $this->list($cli, $relationships),
                'Finalizar' => null,
            };

            if ($action === 'Finalizar') break;
        }

        return $relationships;
    }

    private function add(Command $cli, array &$fields): void
    {
        $name = $cli->ask('Nome da collection (Ex: User)');

        if (! $name || isset($fields[$name])) {
            $cli->warn('Campo inválido ou duplicado.');
            return;
        }

        $type = $cli->choice('Tipo de relacionamento', RelationshipType::values());

        $fields[$name] = $type;
    }

    private function update(Command $cli, array &$relationships): void
    {
        if (empty($relationships)) {
            $cli->warn('Nenhum relacionamento.');
            return;
        }

        $collection = $cli->choice(
            'Escolha a collection para atualizar o relacionamento',
            array_keys($relationships)
        );

        $newType = $cli->choice(
            'Novo tipo de relacionamento',
            RelationshipType::values(),
            0
        );

        $relationships[$collection] = $newType;

        $cli->info("Relacionamento de '{$collection}' atualizado para '{$newType}'.");
    }

    private function remove(Command $cli, array &$fields): void
    {
        if (empty($fields)) return;

        $name = $cli->choice('Escolhar a collection para remover o relacionamento', array_keys($fields));
        unset($fields[$name]);
    }

    private function list(Command $cli, array $fields): void
    {
        $cli->table(['Collection', 'Relacionamento'], collect($fields)->map(fn ($t, $n) => [$n, $t]));
    }
}
