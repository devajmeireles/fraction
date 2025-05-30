<?php

declare(strict_types=1);

namespace Fraction\Console;

use Illuminate\Console\Command;

/** @codeCoverageIgnore */
class ActionOptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'action:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the cached action files.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = config('fraction.path');

        if (! is_dir($path)) {
            $this->components->error("The path [{$path}] does not exist. Skipping optimization.");

            return self::SUCCESS;
        }

        $files = glob($path.'/*.php');

        if (empty($files)) {
            $this->components->error('No action files found to optimize. Skipping optimization.');

            return self::SUCCESS;
        }

        $cached = [];

        foreach ($files as $file) {
            $content = file_get_contents($file);

            if (mb_strpos($content, 'namespace') !== false || mb_strpos($content, 'execute') === false) {
                continue;
            }

            $cached[] = $file;
        }

        if ($cached === []) {
            $this->components->error('No action files found to optimize. Skipping optimization.');

            return self::SUCCESS;
        }

        if (file_exists(base_path('bootstrap/cache/actions.php'))) {
            @unlink(base_path('bootstrap/cache/actions.php'));
        }

        file_put_contents(
            base_path('bootstrap/cache/actions.php'),
            '<?php return '.var_export($cached, true).';'
        );

        $this->components->info('Action files optimized successfully.');

        return self::SUCCESS;
    }
}
