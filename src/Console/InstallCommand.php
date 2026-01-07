<?php

namespace JacquesTredoux\VueSidebarMenu\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vue-sidebar-menu:install 
                            {--migrate : Run migrations after publishing}
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Vue Sidebar Menu package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing Vue Sidebar Menu package...');

        // Publish migrations
        $this->info('Publishing migrations...');
        $this->call('vendor:publish', [
            '--tag' => 'vue-sidebar-menu-migrations',
            '--force' => $this->option('force'),
        ]);

        // Publish Vue components
        $this->info('Publishing Vue components...');
        $this->call('vendor:publish', [
            '--tag' => 'vue-sidebar-menu-components',
            '--force' => $this->option('force'),
        ]);

        // Publish config
        $this->info('Publishing config file...');
        $this->call('vendor:publish', [
            '--tag' => 'vue-sidebar-menu-config',
            '--force' => $this->option('force'),
        ]);

        // Run migrations if requested
        if ($this->option('migrate')) {
            $this->info('Running migrations...');
            $this->call('migrate');
        }

        $this->info('Installation complete!');
        $this->newLine();
        $this->info('Next steps:');
        $this->line('1. Run migrations: php artisan migrate');
        $this->line('2. Include <SidebarMenu /> component in your layout');
        $this->line('3. Share menu data via Inertia: Inertia::share(\'menu\', $menuWebService->getMenu())');
        $this->line('4. (Optional) Add HasFavoriteMenuItems trait to your User model');

        return Command::SUCCESS;
    }
}

