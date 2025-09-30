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

namespace Modular\DependencyGraph\Test\PowerModule;

use Modular\DependencyGraph\Graph\DependencyGraph;
use Modular\DependencyGraph\PowerModule\Setup\DependencyGraphSetup;
use Modular\DependencyGraph\Test\Stub\DatabaseModule;
use Modular\DependencyGraph\Test\Stub\LoggerModule;
use Modular\DependencyGraph\Test\Stub\UserModule;
use Modular\Framework\App\Config\Config as AppConfig;
use Modular\Framework\Container\ConfigurableContainer;
use Modular\Framework\PowerModule\Contract\PowerModule;
use Modular\Framework\PowerModule\Setup\PowerModuleSetupDto;
use Modular\Framework\PowerModule\Setup\SetupPhase;
use PHPUnit\Framework\TestCase;

class DependencyGraphSetupTest extends TestCase
{
    public function testSetupCollectsModuleOnPrePhase(): void
    {
        $rootContainer = new ConfigurableContainer();
        $graph = new DependencyGraph();
        $setup = new DependencyGraphSetup($graph);

        // Simulate registering three modules
        $setup->setup($this->makeDto(new DatabaseModule(), SetupPhase::Pre, $rootContainer));
        $setup->setup($this->makeDto(new LoggerModule(), SetupPhase::Pre, $rootContainer));
        $setup->setup($this->makeDto(new UserModule(), SetupPhase::Pre, $rootContainer));

        $collected = $setup->getDependencyGraph();

        self::assertSame($graph, $collected);
        self::assertSame(3, $collected->getModuleCount());

        // Verify modules are present
        self::assertTrue($collected->hasModule(DatabaseModule::class));
        self::assertTrue($collected->hasModule(LoggerModule::class));
        self::assertTrue($collected->hasModule(UserModule::class));

        // Verify edges from UserModule imports
        self::assertSame(2, $collected->getEdgeCount());
        $edges = $collected->getOutgoingEdges(UserModule::class);
        self::assertCount(2, $edges);
        $targets = array_map(static fn ($e) => $e->toModule, $edges);
        self::assertContains(DatabaseModule::class, $targets);
        self::assertContains(LoggerModule::class, $targets);

        // Verify shortName extraction used in ModuleNode creation
        $userNode = $collected->getModule(UserModule::class);
        self::assertNotNull($userNode);
        self::assertSame('UserModule', $userNode->shortName);
        self::assertTrue($rootContainer->has(DependencyGraph::class));
    }

    public function testSetupDoesNothingOnPostPhase(): void
    {
        $setup = new DependencyGraphSetup();
        $rootContainer = new ConfigurableContainer();

        // Calling in Post phase should not add anything
        $setup->setup($this->makeDto(new DatabaseModule(), SetupPhase::Post, $rootContainer));
        $setup->setup($this->makeDto(new LoggerModule(), SetupPhase::Post, $rootContainer));

        $graph = $setup->getDependencyGraph();
        self::assertSame(0, $graph->getModuleCount());
        self::assertSame(0, $graph->getEdgeCount());
        self::assertFalse($rootContainer->has(DependencyGraph::class));
    }

    private function makeDto(
        PowerModule $module,
        SetupPhase $phase,
        ConfigurableContainer $rootContainer,
    ): PowerModuleSetupDto {
        return new PowerModuleSetupDto(
            setupPhase: $phase,
            powerModule: $module,
            rootContainer: $rootContainer,
            moduleContainer: new ConfigurableContainer(),
            modularAppConfig: AppConfig::forAppRoot('/tmp'),
        );
    }
}
