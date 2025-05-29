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

    // TODO docs
    protected function getPath($name): string
    {
        $this->files->ensureDirectoryExists($path = $this->laravel['path'].'/Actions');

        $name = Str::of($name)
            ->afterLast('App\\')
            ->value();

        return $path.'/'.str_replace('\\', '/', $name).'.php';
    }

    // TODO docs why?
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
