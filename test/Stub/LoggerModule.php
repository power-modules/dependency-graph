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

namespace Modular\DependencyGraph\Test\Stub;

use Modular\Framework\Container\ConfigurableContainerInterface;
use Modular\Framework\PowerModule\Contract\ExportsComponents;
use Modular\Framework\PowerModule\Contract\PowerModule;

class LoggerModule implements PowerModule, ExportsComponents
{
    public function register(ConfigurableContainerInterface $container): void
    {
        // Mock implementation
    }

    public static function exports(): array
    {
        return ['Logger'];
    }
}
