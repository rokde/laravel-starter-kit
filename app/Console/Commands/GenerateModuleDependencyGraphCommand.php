<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @codeCoverageIgnore
 */
class GenerateModuleDependencyGraphCommand extends Command
{
    protected $signature = 'modules:graph {--output=docs/module-dependency-graph.md : The output file path}';

    protected $description = 'Generate a module dependency graph';

    private array $modules = [];

    private array $dependencies = [];

    public function handle(): int
    {
        $this->info('Generating module dependency graph...');

        $this->discoverModules();
        $this->analyzeDependencies();
        $this->generateGraph();

        $this->info('Module dependency graph generated successfully!');

        return self::SUCCESS;
    }

    private function discoverModules(): void
    {
        $modulesPath = base_path('app-modules');
        $directories = File::directories($modulesPath);

        foreach ($directories as $directory) {
            $moduleName = basename((string) $directory);
            $this->modules[] = $moduleName;
            $this->info("Discovered module: {$moduleName}");
        }
    }

    private function analyzeDependencies(): void
    {
        foreach ($this->modules as $module) {
            $this->dependencies[$module] = [];

            // Check composer.json dependencies
            $composerJsonPath = base_path("app-modules/{$module}/composer.json");
            if (File::exists($composerJsonPath)) {
                $composerJson = json_decode(File::get($composerJsonPath), true);
                if (isset($composerJson['require'])) {
                    foreach ($composerJson['require'] as $dependency => $version) {
                        if (Str::startsWith($dependency, 'modules/')) {
                            $dependencyModule = Str::after($dependency, 'modules/');
                            if (in_array($dependencyModule, $this->modules)) {
                                $this->dependencies[$module][] = $dependencyModule;
                                $this->info("Found dependency: {$module} -> {$dependencyModule} (from composer.json)");
                            }
                        }
                    }
                }
            }

            // Analyze PHP files for imports
            $this->analyzePhpFiles($module);
        }
    }

    private function analyzePhpFiles(string $module): void
    {
        $srcPath = base_path("app-modules/{$module}/src");
        if (! File::exists($srcPath)) {
            return;
        }

        $files = File::allFiles($srcPath);
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getPathname());

            // Check for use statements that reference other modules
            foreach ($this->modules as $otherModule) {
                if ($module === $otherModule) {
                    continue;
                }

                $moduleNamespace = $this->getModuleNamespace($otherModule);
                if (preg_match('/use\s+'.preg_quote($moduleNamespace, '/').'\\\\/', $content) && ! in_array($otherModule, $this->dependencies[$module])) {
                    $this->dependencies[$module][] = $otherModule;
                    $this->info("Found dependency: {$module} -> {$otherModule} (from PHP imports)");
                }
            }
        }
    }

    private function getModuleNamespace(string $module): string
    {
        // Convert module name to namespace format (e.g., foundation-layout -> FoundationLayout)
        return 'Modules\\'.Str::studly($module);
    }

    private function generateGraph(): void
    {
        $outputPath = $this->option('output');

        $markdown = "# Module Dependency Graph\n\n";
        $markdown .= "This graph shows the dependencies between modules in the Laravel Starter Kit.\n\n";
        $markdown .= "```mermaid\ngraph TD;\n";

        // Add nodes
        foreach ($this->modules as $module) {
            $moduleId = Str::camel($module);
            $moduleName = Str::studly($module);
            $markdown .= "    {$moduleId}[\"{$moduleName}\"];\n";
        }

        // Add edges
        foreach ($this->dependencies as $module => $dependencies) {
            $moduleId = Str::camel($module);
            foreach ($dependencies as $dependency) {
                $dependencyId = Str::camel($dependency);
                $markdown .= "    {$moduleId} --> {$dependencyId};\n";
            }
        }

        $markdown .= "```\n\n";
        $markdown .= "## Module Details\n\n";

        // Add module details
        foreach ($this->modules as $module) {
            $moduleName = Str::studly($module);
            $markdown .= "### {$moduleName}\n\n";

            // Add module description from composer.json
            $composerJsonPath = base_path("app-modules/{$module}/composer.json");
            if (File::exists($composerJsonPath)) {
                $composerJson = json_decode(File::get($composerJsonPath), true);
                if (isset($composerJson['description'])) {
                    $markdown .= "{$composerJson['description']}\n\n";
                }
            }

            // Add dependencies
            if (! empty($this->dependencies[$module])) {
                $markdown .= "**Dependencies:**\n\n";
                foreach ($this->dependencies[$module] as $dependency) {
                    $dependencyName = Str::studly($dependency);
                    $markdown .= "- {$dependencyName}\n";
                }
                $markdown .= "\n";
            } else {
                $markdown .= "**No dependencies**\n\n";
            }

            // Add dependents
            $dependents = [];
            foreach ($this->dependencies as $otherModule => $dependencies) {
                if (in_array($module, $dependencies)) {
                    $dependents[] = $otherModule;
                }
            }

            if ($dependents !== []) {
                $markdown .= "**Used by:**\n\n";
                foreach ($dependents as $dependent) {
                    $dependentName = Str::studly($dependent);
                    $markdown .= "- {$dependentName}\n";
                }
                $markdown .= "\n";
            } else {
                $markdown .= "**Not used by other modules**\n\n";
            }
        }

        File::put(base_path($outputPath), $markdown);
        $this->info("Graph written to {$outputPath}");
    }
}
