<?php

namespace Sysvale\CuidsGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Sysvale\CuidsGenerator\Blueprint\BlueprintWriter;
use Sysvale\CuidsGenerator\Blueprint\Builders\DraftBuilder;
use Sysvale\CuidsGenerator\Console\Editors\FieldEditor;
use Sysvale\CuidsGenerator\Console\Editors\RelationshipEditor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessorRunner;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\MongoCastCleanupPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\MongoModelPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\MongoSoftDeletesPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\MongoVisibilityPostProcessor;

class CuidsGenerateCommand extends Command
{
    protected $signature = 'cuids:generate';

    protected $description = 'Gera um novo módulo CUIDS';

    protected $blueprintWriter;
    protected $draftBuilder;
    protected $fieldEditor;
    protected $relationshipEditor;
    protected $postProcessorRunner;

    public function __construct(
        BlueprintWriter $blueprintWriter,
        DraftBuilder $draftBuilder,
        FieldEditor $fieldEditor,
        RelationshipEditor $relationshipEditor,
        PostProcessorRunner $postProcessorRunner
    ) {
        parent::__construct();

        $this->blueprintWriter = $blueprintWriter;
        $this->draftBuilder = $draftBuilder;
        $this->fieldEditor = $fieldEditor;
        $this->relationshipEditor = $relationshipEditor;
        $this->postProcessorRunner = $postProcessorRunner;
    }

    public function handle()
    {
        $entity = $this->ask('Qual o nome do model (em inglês)?');

        $fields = $this->fieldEditor->run($this);
        $relationships = [];

        $entityStudly = Str::studly(Str::singular($entity));

        $this->newLine();

        if ($this->confirm('Adicionar relacionamentos?')) {
            $this->newLine();

            $relationships = $this->relationshipEditor->run($this);
        }

        $draft = $this->draftBuilder->build($entityStudly, $fields, $relationships);

        $this->blueprintWriter->write($draft);

        $this->call('blueprint:build');

        try {
            $this->postProcessorRunner->run($entityStudly);
        } catch (\Exception $e) {
            $this->error("Erro ao aplicar pós-processadores]: {$e->getMessage()}");
        }
    }
}

 