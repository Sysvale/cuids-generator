<p align="center">
  <img
    src="https://github.com/user-attachments/assets/f5de8089-e4ca-4235-8a4d-4695731e58a9"
    width="600"
    alt="Gemini Generated Image"
  />
</p>


# Cuids Generator 🚀

[![Tests](https://github.com/sysvale/cuids-generator/actions/workflows/tests.yml/badge.svg)](https://github.com/sysvale/cuids-generator/actions)
[![License](https://img.shields.io/badge/license-Apache--2.0-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-8892bf.svg)](https://www.php.net/)

O **Cuids Generator** é uma ferramenta de linha de comando para Laravel projetada para acelerar a criação de módulos baseados em MongoDB. Ele atua como uma interface interativa sobre o `laravel-shift/blueprint`, permitindo definir modelos, atributos e relacionamentos de forma guiada.

---

## 📋 Requisitos

Antes de começar, verifique se seu ambiente atende aos requisitos mínimos:

- **PHP**: `^8.1` (Recomendado `8.3`)
- **Laravel Framework**: `10.0` ou superior
- **Driver MongoDB**: Configurado e funcional em sua aplicação Laravel.

---

## ⚙️ Instalação

Adicione o repositório em seu `composer.json`
```json
"repositories": [
	{
		"name": "sysvale/cuids-generator",
		"type": "git",
		"url": "https://github.com/Sysvale/cuids-generator"
	}
]

```
Para executar uma branch específica, execute o seguinte comando:

```bash
composer require sysvale/cuids-generator:dev-<nome-branch> -W
```

Desenvolvimento


## 🛠️ Como usar

Executando o  comando:

```bash
php artisan cuids:generate
```
O que o comando faz:
- Interface Interativa: Pergunta o nome do Model (em inglês) e guia você na criação de campos (tipos, obrigatoriedade, etc...);
- Detecção de Models: Escaneia automaticamente sua pasta app/Models para sugerir relacionamentos com entidades existentes;
- Draft Automation: Gera o arquivo draft.yaml formatado para o Blueprint;
- Build Integrado: Executa comando blueprint:build em modo silencioso, gerando Models, Controllers e Migrations;
- Pós-processamento: Cria arquivos de constantes para o frontend e executa runners de limpeza e ajuste de código para o padrão CUIDS.

## 🧪 Desenvolvimento e Testes
Este pacote utiliza o Pest PHP para garantir cobertura do comando de geração de módulos, verificando a cobertura com o coverage.
- Para rodar os testes:
```bash
    composer test
```
- Cobertura
```bash
    composer test:coverage
```
- Checar estilo
```bash
    composer lint
```

- Corrigir estilo
```bash
    composer fix
```

## 🏗️ Estrutura de pastas
## Estrutura de pastas

* `src/`:
	+ `Blueprint/`:
		- Lógica de escrita, Builders de rascunho e Pós-processadores
	+ `Console/`:
		- `Commands/`: CuidsGenerateCommand (Orquestrador)
		- `Editors/`: Editores interativos (FieldEditor e RelationshipEditor)
	+ `Providers/`: Service Provider do Pacote
	+ `Support/`: Enums de Tipos e Helpers
* `tests/`: Suíte de testes com Mocks de dependências

## 📄 Licença
Este projeto está licenciado sob a Apache License 2.0.
