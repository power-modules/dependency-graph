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

namespace Modular\DependencyGraph\PowerModule;

use Modular\DependencyGraph\Graph\ModuleNode;
use Modular\Framework\PowerModule\Contract\ExportsComponents;
use Modular\Framework\PowerModule\Contract\ImportsComponents;

class ModuleNodeFactory
{
    public static function fromPowerModuleClassName(string $className): ModuleNode
    {
        // Extract the short name from the class name
        $shortName = basename(str_replace('\\', '/', $className));
        $exports = is_a($className, ExportsComponents::class, true) ? $className::exports() : [];
        $imports = is_a($className, ImportsComponents::class, true) ? $className::imports() : [];

        return new ModuleNode(
            className: $className,
            shortName: $shortName,
            exports: $exports,
            imports: $imports,
        );
    }
}
