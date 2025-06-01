<?php

declare(strict_types=1);

namespace Fraction\Console;

use Fraction\Facades\Fraction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SplFileInfo;

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
        $used  = [];
        $path  = base_path('app/');
        $files = collect(File::allFiles($path))->filter(fn (SplFileInfo $file) => str_ends_with($file->getFilename(), '.php'));

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $content = file_get_contents($file->getRealPath());

            preg_match_all("/run\(\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $matches);

            foreach ($matches[1] as $match) {
                $used[$match][] = $file->getRelativePathname();
            }
        }

        if (empty($used)) {
            $this->components->warn('No actions found in the codebase.');

            return self::SUCCESS;
        }

        $actions = Fraction::all();

        $defined   = array_values(array_unique(array_column($actions, 'action')));
        $undefined = array_diff(array_keys($used), $defined);

        if (! empty($undefined)) {
            $this->components->warn(count($undefined).' occurrences found');

            $rows = [];

            foreach ($undefined as $action) {
                $files  = implode(', ', array_unique($used[$action]));
                $rows[] = [$files, $action];
            }

            table(headers: ['File', 'Unregistered Action'], rows: $rows);

            return self::FAILURE;
        }

        $this->components->info('No wrong actions found in the codebase.');

        return self::SUCCESS;
    }
}
