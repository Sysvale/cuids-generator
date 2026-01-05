<?php

namespace Sysvale\CuidsGenerator\Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Sysvale\CuidsGenerator\Tests\TestCase;
use Sysvale\CuidsGenerator\Console\Editors\FieldEditor;
use Sysvale\CuidsGenerator\Console\Editors\RelationshipEditor;
use Sysvale\CuidsGenerator\Blueprint\BlueprintWriter;
use Sysvale\CuidsGenerator\Blueprint\Builders\DraftBuilder;
use Sysvale\CuidsGenerator\Blueprint\Builders\FormFieldBuilder;
use Sysvale\CuidsGenerator\Blueprint\PostProcessorRunner;

class CuidsGenerateCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->booted(function () {
            Artisan::command('blueprint:build', fn() => 0);
        });
    }

    public function testCanGenerateModuleWithRelationships()
    {
        $fields = ['name' => [
            'name' => 'name',
            'type' => 'string',
            'required' => true,
            'nullable' => false
            ]
        ];

        $relationships = ['User' => 'belongsTo'];
        $draft = ['models' => ['Supervisor' => []]];

        $this->mock(FieldEditor::class)
            ->shouldReceive('run')
            ->once()
            ->andReturn($fields);

        $this->mock(RelationshipEditor::class)
            ->shouldReceive('run')
            ->once()
            ->with(\Mockery::any())
            ->andReturn($relationships);

        $this->mock(DraftBuilder::class)
            ->shouldReceive('build')
            ->once()
            ->andReturn($draft);

        $this->mock(BlueprintWriter::class)
            ->shouldReceive('write')
            ->once();
        $this->mock(FormFieldBuilder::class)
            ->shouldReceive('handle')
            ->once();

        $this->mock(PostProcessorRunner::class)
            ->shouldReceive('run')
            ->once();

        $this->artisan('cuids:generate')
            ->expectsQuestion('Qual o nome do model (em inglês)?', 'Supervisor')
            ->expectsConfirmation('Deseja adicionar relacionamentos?', 'yes')
            ->assertExitCode(0);
    }

    public function testHandleExceptionDuringGenerationAndExit()
    {
        $this->mock(FieldEditor::class)->shouldReceive('run')->andReturn([]);

        $this->mock(DraftBuilder::class)
            ->shouldReceive('build')
            ->andThrow(new \Exception('Erro Simulado'));

        $this->artisan('cuids:generate')
            ->expectsQuestion('Qual o nome do model (em inglês)?', 'Supervisor')
            ->expectsConfirmation('Deseja adicionar relacionamentos?', 'no')
            ->expectsOutput('Falha na geração: Erro Simulado')
            ->assertExitCode(1);
    }

    public function testShowsNoteWhenSkippingRelationships()
    {
        $this->mock(FieldEditor::class)
            ->shouldReceive('run')
            ->andReturn([]);
        $this->mock(DraftBuilder::class)
            ->shouldReceive('build')
            ->andReturn(['models' => []]);

        $this->mock(BlueprintWriter::class)
            ->shouldReceive('write');

        $this->mock(FormFieldBuilder::class)
            ->shouldReceive('handle');

        $this->mock(PostProcessorRunner::class)
            ->shouldReceive('run');

        $this->artisan('cuids:generate')
            ->expectsQuestion('Qual o nome do model (em inglês)?', 'Supervisor')
            ->expectsConfirmation('Deseja adicionar relacionamentos?', 'no')
            ->expectsOutputToContain('Nenhum relacionamento configurado.')
            ->assertExitCode(0);
    }
}
