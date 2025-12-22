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
        $entity = text('Qual o nome do model (em inglês)?');

        $fields = $this->fieldEditor->run();

        $relationships = [];

        $entityStudly = Str::studly(Str::singular($entity));

        $externalModels = $this->getProjectModels();

        $this->newLine();

        if (confirm(
            label: 'Deseja adicionar relacionamentos a este model?',
            default: true,
            yes: 'Sim, configurar agora',
            no: 'Não, pular esta estapa'
        )) {
            $relationships = $this->relationshipEditor->run($externalModels);
        } else {
            note('Nenhum relacionamento configurado.');
        }

        $draft = $this->draftBuilder->build($entityStudly, $fields, $relationships);

        $this->blueprintWriter->write($draft);

        $this->call('blueprint:build');

        $this->info('Gerando arquivo de constantes para o frontend...');

        try {
            $this->formFieldBuilder->handle($entityStudly, $fields);
        } catch (\Exception $e) {
            $this->error("Erro ao gerar arquivo de constantes do frontend: {$e->getMessage()}");
        }

        $this->info('Executando pós-processadores...');

        try {
            $this->postProcessorRunner->run($entityStudly);
            $this->info('Arquivos gerados com sucesso!');
        } catch (\Exception $e) {
            $this->error("Erro ao aplicar pós-processadores]: {$e->getMessage()}");
        }
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
