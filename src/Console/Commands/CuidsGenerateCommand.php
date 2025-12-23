<?php

namespace Sysvale\CuidsGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Sysvale\CuidsGenerator\Blueprint\BlueprintWriter;
use Sysvale\CuidsGenerator\Blueprint\Builders\DraftBuilder;
use Sysvale\CuidsGenerator\Console\Editors\FieldEditor;
use Sysvale\CuidsGenerator\Console\Editors\RelationshipEditor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessorRunner;
use Sysvale\CuidsGenerator\Blueprint\Builders\FormFieldBuilder;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;

class CuidsGenerateCommand extends Command
{
    protected $signature = 'cuids:generate';
    protected $description = 'Gera um novo módulo CUIDS';
    protected $blueprintWriter;
    protected $draftBuilder;
    protected $fieldEditor;
    protected $relationshipEditor;
    protected $postProcessorRunner;
    protected $formFieldBuilder;

    public function __construct(
        BlueprintWriter $blueprintWriter,
        DraftBuilder $draftBuilder,
        FieldEditor $fieldEditor,
        RelationshipEditor $relationshipEditor,
        PostProcessorRunner $postProcessorRunner,
        FormFieldBuilder $formFieldBuilder,
    ) {
        parent::__construct();

        $this->blueprintWriter = $blueprintWriter;
        $this->draftBuilder = $draftBuilder;
        $this->fieldEditor = $fieldEditor;
        $this->relationshipEditor = $relationshipEditor;
        $this->postProcessorRunner = $postProcessorRunner;
        $this->formFieldBuilder = $formFieldBuilder;
    }

    public function handle()
    {
        $entity = $this->askForEntityName();
        $fields = $this->fieldEditor->run();

        $entityStudly = Str::studly(Str::singular($entity));

        $relationships = $this->askForRelationships();

        try {
            $this->components->info('Gerando rascunho do Blueprint...');
            $draft = $this->draftBuilder->build($entityStudly, $fields, $relationships);
            $this->blueprintWriter->write($draft);

            spin(fn () => $this->callSilent('blueprint:build'), 'Construindo arquivos via Blueprint...');

            $this->components->info('Gerando constantes do frontend...');
            $this->formFieldBuilder->handle($entityStudly, $fields);

            $this->components->info('Aplicando pós-processadores...');
            $this->postProcessorRunner->run($entityStudly);

            $this->components->info("Módulo {$entityStudly} gerado com sucesso!");
            
        } catch (\Exception $e) {
            $this->error("Falha na geração: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function askForEntityName(): string
    {
        return text(
            label: 'Qual o nome do model (em inglês)?',
            validate: fn ($value) => empty($value) ? 'Obrigatório' : null
        );
    }

    protected function askForRelationships(): array
    {
        $this->newLine();
        
        $wantsRelationships = confirm(
            label: 'Deseja adicionar relacionamentos?',
            default: true,
            yes: 'Sim, configurar',
            no: 'Pular'
        );

        if (!$wantsRelationships) {
            note('Nenhum relacionamento configurado.');
            return [];
        }

        return $this->relationshipEditor->run($this->getProjectModels());
    }

    public function getProjectModels(): array
    {
        $modelsPath = app_path('Models');

        if (! File::isDirectory($modelsPath)) return [];

        $files = File::allFiles($modelsPath);

        return collect($files)->map(function ($file) {
            return Str::replaceLast('.php', '', $file->getFilename());
        })->toArray();
    }
}
