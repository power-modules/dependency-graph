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

namespace Modular\DependencyGraph\Test\Graph;

use Modular\DependencyGraph\Graph\DependencyGraph;
use Modular\DependencyGraph\Graph\ModuleNode;
use Modular\DependencyGraph\Test\Stub\DatabaseModule;
use Modular\DependencyGraph\Test\Stub\LoggerModule;
use Modular\DependencyGraph\Test\Stub\UnusedModule;
use Modular\Framework\PowerModule\ImportItem;
use PHPUnit\Framework\TestCase;

class DependencyGraphTest extends TestCase
{
    public function testAddModule(): void
    {
        $graph = new DependencyGraph();

        $module = new ModuleNode(
            className: DatabaseModule::class,
            shortName: 'DatabaseModule',
            exports: ['DatabaseConnection', 'QueryBuilder'],
            imports: [],
        );

        $graph->addModule($module);

        $this->assertSame(1, $graph->getModuleCount());
        $this->assertSame($module, $graph->getModule(DatabaseModule::class));
        $this->assertTrue($graph->hasModule(DatabaseModule::class));
    }

    public function testAddModuleWithDependencies(): void
    {
        $graph = new DependencyGraph();

        // Add a module with imports
        $userModule = new ModuleNode(
            className: 'UserModule',
            shortName: 'UserModule',
            exports: ['UserService'],
            imports: [
                ImportItem::create(DatabaseModule::class, 'DatabaseConnection', 'QueryBuilder'),
            ],
        );

        $graph->addModule($userModule);

        $this->assertSame(1, $graph->getModuleCount());
        $this->assertSame(1, $graph->getEdgeCount());

        $edges = $graph->getEdges();
        $edge = $edges[0];

        $this->assertSame('UserModule', $edge->fromModule);
        $this->assertSame(DatabaseModule::class, $edge->toModule);
        $this->assertSame(['DatabaseConnection', 'QueryBuilder'], $edge->importedServices);
    }

    public function testGetIndependentModules(): void
    {
        $graph = new DependencyGraph();

        // Add independent module
        $independentModule = new ModuleNode(
            className: LoggerModule::class,
            shortName: 'LoggerModule',
            exports: ['Logger'],
            imports: [],
        );

        // Add dependent module
        $dependentModule = new ModuleNode(
            className: 'UserModule',
            shortName: 'UserModule',
            exports: ['UserService'],
            imports: [
                ImportItem::create(LoggerModule::class, 'Logger'),
            ],
        );

        $graph->addModule($independentModule);
        $graph->addModule($dependentModule);

        $independentModules = $graph->getIndependentModules();

        $this->assertCount(1, $independentModules);
        $this->assertSame($independentModule, reset($independentModules));
    }

    public function testGetUnusedModules(): void
    {
        $graph = new DependencyGraph();

        // Add unused module (exports but no other module imports from it)
        $unusedModule = new ModuleNode(
            className: UnusedModule::class,
            shortName: 'UnusedModule',
            exports: ['UnusedService'],
            imports: [],
        );

        // Add module that is used
        $usedModule = new ModuleNode(
            className: DatabaseModule::class,
            shortName: 'DatabaseModule',
            exports: ['DatabaseConnection'],
            imports: [],
        );

        // Add module that uses DatabaseModule
        $consumerModule = new ModuleNode(
            className: 'UserModule',
            shortName: 'UserModule',
            exports: ['UserService'],
            imports: [
                ImportItem::create(DatabaseModule::class, 'DatabaseConnection'),
            ],
        );

        $graph->addModule($unusedModule);
        $graph->addModule($usedModule);
        $graph->addModule($consumerModule);

        $unusedModules = $graph->getUnusedModules();

        $this->assertCount(2, $unusedModules); // UnusedModule and UserModule (UserModule is not imported by anyone)

        $unusedModuleNames = array_map(
            static fn (ModuleNode $module): string => $module->className,
            $unusedModules,
        );

        $this->assertContains(UnusedModule::class, $unusedModuleNames);
        $this->assertContains('UserModule', $unusedModuleNames);
        $this->assertNotContains(DatabaseModule::class, $unusedModuleNames);
    }

    public function testGetOutgoingAndIncomingEdges(): void
    {
        $graph = new DependencyGraph();

        $userModule = new ModuleNode(
            className: 'UserModule',
            shortName: 'UserModule',
            exports: ['UserService'],
            imports: [
                ImportItem::create(DatabaseModule::class, 'DatabaseConnection'),
                ImportItem::create(LoggerModule::class, 'Logger'),
            ],
        );

        $graph->addModule($userModule);

        // Test outgoing edges
        $outgoingEdges = $graph->getOutgoingEdges('UserModule');
        $this->assertCount(2, $outgoingEdges);

        // Test incoming edges for DatabaseModule
        $incomingEdges = $graph->getIncomingEdges(DatabaseModule::class);
        $this->assertCount(1, $incomingEdges);
        $this->assertSame('UserModule', $incomingEdges[0]->fromModule);

        // Test non-existent module
        $noEdges = $graph->getIncomingEdges('NonExistentModule');
        $this->assertCount(0, $noEdges);
    }
}
