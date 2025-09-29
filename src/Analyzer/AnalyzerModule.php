<?php

/**
 * This file is part of the Modular Framework package.
 *
 * (c) 2025 Evgenii Teterin
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Modular\DependencyGraph\Analyzer;

use Modular\Framework\Container\ConfigurableContainerInterface;
use Modular\Framework\PowerModule\Contract\ExportsComponents;
use Modular\Framework\PowerModule\Contract\PowerModule;

class AnalyzerModule implements PowerModule, ExportsComponents
{
    public static function exports(): array
    {
        return [
            AnalyzerPluginRegistry::class,
        ];
    }

    public function register(ConfigurableContainerInterface $container): void
    {
        $container->set(AnalyzerPluginRegistry::class, AnalyzerPluginRegistry::class);
        // TODO: implement plugin orchestrator service
    }
}
