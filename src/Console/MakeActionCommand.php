<?php

declare(strict_types=1);

namespace Fraction\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'make:action')]
class MakeActionCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new fraction file.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    protected function getStub(): string
    {
        return __DIR__.'/../stubs/fraction.stub';
    }

    // The reason why publishing this method is to ensure the
    // action will be created in the correct path fraction.path
    protected function getPath($name): string
    {
        $path = config('fraction.path');

        if (empty($path)) {
            $path = $this->laravel['path'].'/Actions';
        }

        $this->files->ensureDirectoryExists($path);

        $name = Str::of($name)
            ->afterLast('App\\')
            ->value();

        return $path.'/'.str_replace('\\', '/', $name).'.php';
    }

    // The reason why publishing this method is to ensure override the {{ name }}
    // placeholder in the stub file with the formatted name of the action
    protected function buildClass($name): string
    {
        $stub = $this->files->get($this->getStub());

        $formatted = Str::of($this->getNameInput())
            ->afterLast('\\')
            ->headline()
            ->lower()
            ->snake(' ')
            ->value();

        return str_replace('{{ name }}', $formatted, $stub);
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => [
                'What should the action be named?',
                'E.g. CreateUserAction',
            ],
        ];
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the action'],
        ];
    }
}
