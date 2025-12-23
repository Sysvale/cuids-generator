<?php

namespace Sysvale\CuidsGenerator\Console\Editors;

use Sysvale\CuidsGenerator\Support\RelationshipType;
use Illuminate\Console\Command;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\table;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\confirm;

class RelationshipEditor
{
    public function run(array $models): array
    {
        $relationships = [];

        info('Configuração de Relacionamentos (MongoDB)');

        while (true) {
            $action = select(
                label: 'Gerenciar relacionamentos',
                options: [
                    'add' => 'Adicionar novo',
                    'select' => 'Selecionar',
                    'update' => 'Atualizar existente',
                    'remove' => 'Remover',
                    'list'   => 'Listar todos',
                    'done'   => 'Finalizar',
                ],
                default: 'add'
            );

            if ($action === 'done') break;

            match ($action) {
                'add'    => $this->add($relationships),
                'select' => $this->select($relationships, $models),
                'update' => $this->update($relationships),
                'remove' => $this->remove($relationships),
                'list'   => $this->list($relationships),
            };
        }

        return $relationships;
    }

    private function add(array &$relationships): void
    {
        $name = text(
            label: 'Nome do Model relacionado (Ex: User, Category)',
            placeholder: 'Sempre em StudlyCase e singular',
            validate: fn (string $value) => match (true) {
                empty($value) => 'O nome é obrigatório.',
                isset($relationships[$value]) => 'Este relacionamento já foi definido.',
                default => null,
            }
        );

        $type = select(
            label: "Qual o tipo de relacionamento com '{$name}'?",
            options: RelationshipType::values(),
            hint: 'Lembre-se: belongsTo criará um campo _id no documento atual.'
        );

        $relationships[$name] = $type;

        info("Relacionamento '{$name} ({$type})' adicionado.");
    }

    private function select(array &$relationships, array &$models): void
    {

        $model = select(
            label: "Selecione o model para relacionamento",
            options: $models,
            hint: 'Lembre-se: belongsTo criará um campo _id no documento atual.'
        );

        $type = select(
            label: "Qual o tipo de relacionamento com '{$model}'?",
            options: RelationshipType::values(),
            hint: 'Lembre-se: belongsTo criará um campo _id no documento atual.'
        );

        $relationships[$model] = $type;
        info("Relacionamento '{$model} ({$type})' adicionado.");

        $this->list($relationships);
    }

    private function update(array &$relationships): void
    {
        if ($this->isEmpty($relationships)) return;

        $collection = select(
            'Selecione o relacionamento para atualizar',
            array_keys($relationships)
        );

        $newType = select(
            label: "Novo tipo para '{$collection}'",
            options: RelationshipType::values(),
            default: $relationships[$collection]
        );

        $relationships[$collection] = $newType;

        info("Atualizado: '{$collection}' agora é '{$newType}'.");

        $this->list($relationships);
    }

    private function remove(array &$relationships): void
    {
        if ($this->isEmpty($relationships)) return;

        $name = select('Remover qual relacionamento?', array_keys($relationships));

        $removeRelationship = confirm(
            label: "Deseja realmente remover o vínculo com '{$name}'?",
            default: true,
            yes: 'Sim',
            no: 'Não'
        );

        if (!$removeRelationship) return;

        unset($relationships[$name]);
        warning("Relacionamento com '{$name}' removido.");

        $this->list($relationships);
    }

    private function list(array $relationships): void
    {
        if ($this->isEmpty($relationships)) return;

        table(
            ['Model Relacionado', 'Tipo'],
            collect($relationships)->map(fn ($t, $n) => [$n, $t])->toArray()
        );
    }

    private function isEmpty(array $relationships): bool
    {
        if (empty($relationships)) {
            warning('Nenhum relacionamento configurado.');
            return true;
        }
        return false;
    }
}