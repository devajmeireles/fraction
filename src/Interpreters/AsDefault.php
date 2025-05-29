<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreter;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Facades\Fraction;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionException;

final class AsDefault implements ShouldInterpreter
{
    use ShareableInterpreter;

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function handle(Container $container): mixed
    {
        if ($this->before) {
            foreach ($this->before as $hook) {
                $builder = Fraction::get($hook);

                $builder(...$this->arguments);
            }
        }

        $result = $this->dependencies($container)->resolve($this->closure, $this->arguments);

        if ($this->after) {
            foreach ($this->after as $hook) {
                $builder = Fraction::get($hook);

                $builder(...$this->arguments);
            }
        }

        return $result;
    }

    public function hooks(array $before, array $after): self
    {
        $this->before = $before;

        $this->after = $after;

        return $this;
    }
}
