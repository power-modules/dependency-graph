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

use Modular\DependencyGraph\Graph\DependencyGraph;
use Modular\Plugin\Contract\Plugin;

interface Analyzer extends Plugin
{
    /**
     * @return array<string,mixed>
     */
    public function analyze(DependencyGraph $graph): array;
}
