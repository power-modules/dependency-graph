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

namespace Modular\DependencyGraph\Renderer;

use Modular\DependencyGraph\Graph\DependencyGraph;
use Modular\Plugin\Contract\Plugin;

/**
 * Interface for rendering dependency graphs in different formats.
 *
 * Implementations can generate output in various formats and exposed as plugins.
 * For example Mermaid, Graphviz DOT, JSON, or custom visualization formats.
 */
interface Renderer extends Plugin
{
    /**
     * Render a dependency graph to string format.
     *
     * @param DependencyGraph $graph The dependency graph to render
     * @return string The rendered graph in the specific format
     */
    public function render(DependencyGraph $graph): string;

    /**
     * Get the file extension for this renderer's output format.
     *
     * @return string File extension without the dot (e.g., 'mmd', 'dot', 'json')
     */
    public function getFileExtension(): string;

    /**
     * Get the MIME type for this renderer's output format.
     *
     * @return string MIME type (e.g., 'text/plain', 'application/json')
     */
    public function getMimeType(): string;

    /**
     * Get a human-readable description of this renderer.
     *
     * @return string Description of the output format
     */
    public function getDescription(): string;
}
