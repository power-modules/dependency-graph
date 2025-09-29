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

namespace Modular\DependencyGraph\Graph;

/**
 * Main data structure representing the complete dependency graph of modules.
 *
 * Maintains collections of modules and their dependency relationships,
 * providing methods for analysis and traversal.
 */
class DependencyGraph
{
    /** @var array<string, ModuleNode> */
    private array $modules = [];

    /** @var array<DependencyEdge> */
    private array $edges = [];

    /**
     * Add a module to the dependency graph.
     *
     * This automatically builds dependency edges based on the module's imports.
     */
    public function addModule(ModuleNode $module): void
    {
        $this->modules[$module->className] = $module;
        $this->buildEdges($module);
    }

    /**
     * Get all modules in the graph.
     *
     * @return array<string, ModuleNode>
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * Get all dependency edges in the graph.
     *
     * @return array<DependencyEdge>
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * Get a specific module by its class name.
     */
    public function getModule(string $className): ?ModuleNode
    {
        return $this->modules[$className] ?? null;
    }

    /**
     * Check if a module exists in the graph.
     */
    public function hasModule(string $className): bool
    {
        return isset($this->modules[$className]);
    }

    /**
     * Get modules that have no dependencies (leaf nodes in dependency tree).
     *
     * @return array<ModuleNode>
     */
    public function getIndependentModules(): array
    {
        return array_filter(
            $this->modules,
            static fn (ModuleNode $module): bool => !$module->hasImports(),
        );
    }

    /**
     * Get modules that are not depended upon by any other module.
     *
     * @return array<ModuleNode>
     */
    public function getUnusedModules(): array
    {
        $referencedModules = [];
        foreach ($this->edges as $edge) {
            $referencedModules[$edge->toModule] = true;
        }

        return array_filter(
            $this->modules,
            static fn (ModuleNode $module): bool => !isset($referencedModules[$module->className]),
        );
    }

    /**
     * Get edges where the specified module is the source (imports from others).
     *
     * @return array<DependencyEdge>
     */
    public function getOutgoingEdges(string $moduleClassName): array
    {
        return array_filter(
            $this->edges,
            static fn (DependencyEdge $edge): bool => $edge->fromModule === $moduleClassName,
        );
    }

    /**
     * Get edges where the specified module is the target (exports to others).
     *
     * @return array<DependencyEdge>
     */
    public function getIncomingEdges(string $moduleClassName): array
    {
        return array_filter(
            $this->edges,
            static fn (DependencyEdge $edge): bool => $edge->toModule === $moduleClassName,
        );
    }

    /**
     * Get the total number of modules in the graph.
     */
    public function getModuleCount(): int
    {
        return count($this->modules);
    }

    /**
     * Get the total number of dependency edges in the graph.
     */
    public function getEdgeCount(): int
    {
        return count($this->edges);
    }

    /**
     * Check if the graph has any cycles (circular dependencies).
     *
     * Uses depth-first search to detect cycles.
     */
    public function hasCycles(): bool
    {
        $visited = [];
        $recursionStack = [];

        foreach (array_keys($this->modules) as $moduleClassName) {
            if (!isset($visited[$moduleClassName])) {
                if ($this->hasCycleDFS($moduleClassName, $visited, $recursionStack)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Build dependency edges for a module based on its imports.
     */
    private function buildEdges(ModuleNode $module): void
    {
        foreach ($module->imports as $import) {
            $this->edges[] = new DependencyEdge(
                fromModule: $module->className,
                toModule: $import->moduleName,
                importedServices: $import->itemsToImport,
            );
        }
    }

    /**
     * Depth-first search helper for cycle detection.
     *
     * @param array<string, true> $visited
     * @param array<string, true> $recursionStack
     */
    private function hasCycleDFS(string $moduleClassName, array &$visited, array &$recursionStack): bool
    {
        $visited[$moduleClassName] = true;
        $recursionStack[$moduleClassName] = true;

        // Get all outgoing edges from this module
        foreach ($this->getOutgoingEdges($moduleClassName) as $edge) {
            $targetModule = $edge->toModule;

            if (!isset($visited[$targetModule])) {
                if ($this->hasCycleDFS($targetModule, $visited, $recursionStack)) {
                    return true;
                }
            } elseif (isset($recursionStack[$targetModule])) {
                return true; // Back edge found, cycle detected
            }
        }

        unset($recursionStack[$moduleClassName]);

        return false;
    }
}
