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
use Modular\Framework\PowerModule\Contract\ImportsComponents;
use Modular\Framework\PowerModule\Contract\PowerModule;
use Modular\Framework\PowerModule\ImportItem;

class UserModule implements PowerModule, ExportsComponents, ImportsComponents
{
    public function register(ConfigurableContainerInterface $container): void
    {
        // Mock implementation
    }

    public static function exports(): array
    {
        return ['UserService'];
    }

    public static function imports(): array
    {
        return [
            ImportItem::create(DatabaseModule::class, 'DatabaseConnection'),
            ImportItem::create(LoggerModule::class, 'Logger'),
        ];
    }
}
