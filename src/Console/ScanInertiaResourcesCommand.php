<?php

namespace JacquesTredoux\VueSidebarMenu\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JacquesTredoux\VueSidebarMenu\Models\MenuGroup;
use JacquesTredoux\VueSidebarMenu\Models\MenuItem;

class ScanInertiaResourcesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vue-sidebar-menu:scan-inertia-resources
                            {--group-key=inertia-resources : The key for the menu group}
                            {--group-label=Inertia Resources : The label for the menu group}
                            {--namespace=App\\Http\\Resources : The namespace to scan}
                            {--path=app/Http/Resources : The path to scan}
                            {--force : Overwrite existing menu items}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan for InertiaResource classes and create menu items for them';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning for InertiaResource classes...');

        $namespace = $this->option('namespace');
        $path = $this->option('path');
        $groupKey = $this->option('group-key');
        $groupLabel = $this->option('group-label');
        $force = $this->option('force');

        // Get or create the menu group
        $menuGroup = MenuGroup::firstOrCreate(
            ['key' => $groupKey],
            [
                'label' => $groupLabel,
                'icon' => 'heroicon-o-document',
                'sort_order' => 999,
                'is_active' => true,
            ]
        );

        if ($menuGroup->wasRecentlyCreated) {
            $this->info("Created menu group: {$groupLabel}");
        } else {
            $this->info("Using existing menu group: {$groupLabel}");
        }

        // Scan for InertiaResource classes
        $resources = $this->scanForInertiaResources($namespace, $path);

        if (empty($resources)) {
            $this->warn('No InertiaResource classes found.');
            return Command::FAILURE;
        }

        $this->info("Found " . count($resources) . " InertiaResource class(es)");

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($resources as $resource) {
            $result = $this->createMenuItemForResource($resource, $menuGroup, $force);
            
            if ($result === 'created') {
                $created++;
            } elseif ($result === 'updated') {
                $updated++;
            } else {
                $skipped++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  Created: {$created}");
        $this->line("  Updated: {$updated}");
        $this->line("  Skipped: {$skipped}");

        return Command::SUCCESS;
    }

    /**
     * Scan for InertiaResource classes in the given namespace and path
     */
    private function scanForInertiaResources(string $namespace, string $path): array
    {
        $resources = [];
        $basePath = base_path($path);

        if (!File::exists($basePath)) {
            $this->warn("Path does not exist: {$basePath}");
            return $resources;
        }

        // Use allFiles() which already recursively scans directories
        $files = File::allFiles($basePath);

        foreach ($files as $file) {
            // Skip non-PHP files
            if ($file->getExtension() !== 'php') {
                continue;
            }

            // Get relative path from base path
            $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            
            // Remove .php extension
            $relativePath = str_replace('.php', '', $relativePath);
            
            // Convert directory separators to namespace separators
            $relativePath = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
            
            // Build full class name
            $className = $namespace . '\\' . $relativePath;

            // Try to autoload the class
            if (!class_exists($className)) {
                continue;
            }

            try {
                $reflection = new \ReflectionClass($className);
                
                // Skip if not instantiable (abstract, interface, trait)
                if (!$reflection->isInstantiable() && !$reflection->isClass()) {
                    continue;
                }
                
                // Check if class extends InertiaResource
                if ($this->extendsInertiaResource($reflection)) {
                    $resources[] = [
                        'class' => $className,
                        'name' => $reflection->getShortName(),
                        'namespace' => $reflection->getNamespaceName(),
                    ];
                }
            } catch (\ReflectionException $e) {
                $this->warn("Could not reflect class {$className}: " . $e->getMessage());
                continue;
            }
        }

        return $resources;
    }

    /**
     * Check if a class extends InertiaResource
     */
    private function extendsInertiaResource(\ReflectionClass $reflection): bool
    {
        // Skip abstract classes and interfaces
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            return false;
        }

        $parent = $reflection->getParentClass();
        
        if (!$parent) {
            return false;
        }

        $parentName = $parent->getName();

        // Check if parent is InertiaResource (common namespaces)
        if ($parentName === 'Inertia\InertiaResource' || 
            $parentName === 'App\Http\Resources\InertiaResource' ||
            Str::endsWith($parentName, 'InertiaResource')) {
            return true;
        }

        // Recursively check parent classes
        return $this->extendsInertiaResource($parent);
    }

    /**
     * Create a menu item for an InertiaResource
     */
    private function createMenuItemForResource(array $resource, MenuGroup $menuGroup, bool $force): string
    {
        $className = $resource['class'];
        $resourceName = $resource['name'];
        
        // Generate menu key from class name
        $menuKey = Str::kebab(Str::replace('Resource', '', $resourceName));
        
        // Generate label from class name
        $label = Str::title(Str::replace(['Resource', '_'], ['', ' '], $menuKey));
        
        // Try to generate route name
        // Common pattern: vue.{resource-name}.index
        $routeName = 'vue.' . $menuKey . '.index';
        
        // Check if route exists
        $route = \Illuminate\Support\Facades\Route::has($routeName) ? $routeName : null;
        
        // Generate URL if route exists
        $url = $route ? route($route) : '#';

        // Check if menu item already exists
        $menuItem = MenuItem::where('menu_group_id', $menuGroup->id)
            ->where('key', $menuKey)
            ->first();

        if ($menuItem) {
            if (!$force) {
                $this->line("  Skipped: {$label} (already exists, use --force to update)");
                return 'skipped';
            }

            // Update existing item
            $menuItem->update([
                'label' => $label,
                'route' => $route,
                'url' => $url,
                'icon' => 'heroicon-o-document',
                'is_active' => true,
            ]);

            $this->line("  Updated: {$label}");
            return 'updated';
        }

        // Create new menu item
        MenuItem::create([
            'menu_group_id' => $menuGroup->id,
            'key' => $menuKey,
            'label' => $label,
            'icon' => 'heroicon-o-document',
            'url' => $url,
            'route' => $route,
            'permission_name' => null, // Can be set manually later
            'sort_order' => MenuItem::where('menu_group_id', $menuGroup->id)->max('sort_order') + 1,
            'is_active' => true,
        ]);

        $this->line("  Created: {$label}");
        return 'created';
    }
}

