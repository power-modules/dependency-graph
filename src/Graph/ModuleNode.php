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

use Modular\Framework\PowerModule\ImportItem;

/**
 * Represents a module node in the dependency graph.
 *
 * Contains information about a module's exports, imports, and metadata
 * needed for dependency analysis and visualization.
 */
final readonly class ModuleNode
{
    /**
     * @param string $className Full class name of the module
     * @param string $shortName Human-readable short name for visualization
     * @param array<string> $exports Array of exported service class names
     * @param array<ImportItem> $imports Array of ImportItem objects
     */
    public function __construct(
        public string $className,
        public string $shortName,
        public array $exports,
        public array $imports,
    ) {
    }

    /**
     * Get all module names that this module imports from.
     *
     * @return array<string> Array of module class names
     */
    public function getImportedModules(): array
    {
        return array_map(
            static fn (ImportItem $import): string => $import->moduleName,
            $this->imports,
        );
    }

    /**
     * Get all service names that this module imports.
     *
     * @return array<string> Array of service class names
     */
    public function getImportedServices(): array
    {
        $services = [];
        foreach ($this->imports as $import) {
            $services = array_merge($services, $import->itemsToImport);
        }

        return $services;
    }

    /**
     * Check if this module exports any services.
     */
    public function hasExports(): bool
    {
        return !empty($this->exports);
    }

    /**
     * Check if this module imports from any other modules.
     */
    public function hasImports(): bool
    {
        return !empty($this->imports);
    }

    /**
     * Get the number of services this module exports.
     */
    public function getExportCount(): int
    {
        return count($this->exports);
    }

    /**
     * Get the number of services this module imports.
     */
    public function getImportCount(): int
    {
        return count($this->getImportedServices());
    }
}
