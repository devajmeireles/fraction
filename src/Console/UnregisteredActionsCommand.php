<?php

declare(strict_types=1);

namespace Fraction\Console;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\table;

/** @codeCoverageIgnore */
class UnregisteredActionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'actions:unregistered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List of actions used without being registered.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $find = 'run(';

        $windows = windows_os();

        $command = $windows
            ? ['findstr', '/S', '/N', '/I', $find, base_path('app').'\*.php']
            : ['grep', '-rn', $find, base_path('app'), '--include=*.php'];

        $process = new Process($command);

        try {
            $process->mustRun();

            return $this->output($process->getOutput());
        } catch (ProcessFailedException) {
            $this->components->error('No unregistered actions found in the codebase.');
        } catch (Exception $exception) {
            $this->components->error('Unexpected Error: '.$exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Output the results of the search.
     */
    private function output(string $output): int
    {
        if (blank($output)) {
            return self::SUCCESS;
        }

        $rows = [];

        $lines = collect(explode(PHP_EOL, $output))->filter();

        if ($lines->count() === 0) {
            $this->components->info('No unregistered actions found.');

            return self::SUCCESS;
        }

        $this->components->warn('Unregistered actions found:');

        $lines->lazy()->each(function (string $line) use (&$rows): bool {
            preg_match("/^(\/[^\s:]+):\d+:\s*.*?run\(\s*'([^']+)'\s*\)/", $line, $matches);

            if (blank($line) || count($matches) < 3) {
                return false;
            }

            $path = str($matches[0])
                ->afterLast(base_path())
                ->beforeLast(':')
                ->replaceFirst('/', '')
                ->value();

            $rows[] = [$path, $matches[2]];

            return true;
        });

        if ($rows === []) {
            $this->components->info('No unregistered actions found.');

            return self::SUCCESS;
        }

        table(['File', 'Unregistered Action'], $rows);

        return self::FAILURE;
    }
}
