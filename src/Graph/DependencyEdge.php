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
 * Represents a dependency edge between two modules in the dependency graph.
 *
 * An edge indicates that the source module imports services from the target module.
 */
final readonly class DependencyEdge
{
    /**
     * @param string $fromModule Class name of the module that imports
     * @param string $toModule Class name of the module that exports
     * @param array<string> $importedServices Array of service class names being imported
     */
    public function __construct(
        public string $fromModule,
        public string $toModule,
        public array $importedServices,
    ) {
    }

    /**
     * Get the number of services imported through this edge.
     */
    public function getImportedServiceCount(): int
    {
        return count($this->importedServices);
    }

    /**
     * Check if this edge represents a strong coupling (many imported services).
     *
     * @param int $threshold The threshold above which coupling is considered strong
     */
    public function isStrongCoupling(int $threshold = 3): bool
    {
        return $this->getImportedServiceCount() > $threshold;
    }

    /**
     * Get a formatted string representation of imported services.
     *
     * @param int $maxLength Maximum length before truncating
     */
    public function getFormattedServices(int $maxLength = 50): string
    {
        $services = implode(', ', $this->importedServices);

        if (strlen($services) <= $maxLength) {
            return $services;
        }

        return substr($services, 0, $maxLength - 3) . '...';
    }
}
