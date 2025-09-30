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

namespace Modular\DependencyGraph\PowerModule\Setup;

use Modular\DependencyGraph\Graph\DependencyGraph;
use Modular\DependencyGraph\PowerModule\ModuleNodeFactory;
use Modular\Framework\PowerModule\Contract\PowerModuleSetup;
use Modular\Framework\PowerModule\Setup\PowerModuleSetupDto;
use Modular\Framework\PowerModule\Setup\SetupPhase;

/**
 * Main PowerModuleSetup implementation for dependency graph analysis.
 *
 * This setup automatically collects module information during app bootstrap,
 * building a complete dependency graph that can be used for analysis and visualization.
 */
class DependencyGraphSetup implements PowerModuleSetup
{
    public function __construct(
        private DependencyGraph $graph = new DependencyGraph(),
        private ModuleNodeFactory $moduleNodeFactory = new ModuleNodeFactory(),
    ) {
    }

    /**
     * Setup method called by the framework during module registration.
     *
     * Collects module information during the Pre phase, before imports are resolved.
     */
    public function setup(PowerModuleSetupDto $dto): void
    {
        // Collect module information during Pre phase to capture all modules
        if ($dto->setupPhase !== SetupPhase::Pre) {
            return;
        }

        if ($dto->rootContainer->has(DependencyGraph::class) === true) {
            // Use existing graph from container if available
            $this->graph = $dto->rootContainer->get(DependencyGraph::class);
        } else {
            $dto->rootContainer->set(DependencyGraph::class, $this->graph);
        }

        $moduleNode = $this->moduleNodeFactory::fromPowerModuleClassName($dto->powerModule::class);

        $this->graph->addModule($moduleNode);
    }

    /**
     * Get the complete dependency graph after all modules have been processed.
     */
    public function getDependencyGraph(): DependencyGraph
    {
        return $this->graph;
    }
}
