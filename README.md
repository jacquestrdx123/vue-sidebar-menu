# Vue Sidebar Menu

A Laravel Composer package providing a database-driven sidebar menu system with Vue 3 components and Inertia.js integration.

## Features

- Database-driven menu structure (MenuGroups and MenuItems)
- Permission-based menu item visibility
- Nested menu items support
- User favorite menu items
- Search functionality
- Collapsible sidebar
- Responsive design
- Inertia.js integration

## Requirements

- Laravel 9+ or 10+
- Vue 3
- Inertia.js
- PHP 8.1+

## Installation

1. Install the package via Composer:

```bash
composer require jacquestredoux/vue-sidebar-menu
```

2. Run the install command:

```bash
php artisan vue-sidebar-menu:install
```

This will:
- Publish migrations to `database/migrations/`
- Publish Vue components to `resources/js/Components/SidebarMenu/`
- Publish config file to `config/vue-sidebar-menu.php`

3. Run migrations:

```bash
php artisan migrate
```

## Configuration

The config file is published to `config/vue-sidebar-menu.php`. You can customize:

- `cache_time`: Cache duration for user permissions (defaults to 5 minutes in debug, 1 hour in production)
- `member_model`: Member model class name (if you have a separate Member model)
- `user_model`: User model class name (defaults to Laravel auth provider)

## Usage

### 1. Share Menu Data via Inertia

In your middleware or service provider, share the menu data:

```php
use JacquesTredoux\VueSidebarMenu\Services\MenuWebService;
use Inertia\Inertia;

// In your HandleInertiaRequests middleware or similar
$menuService = app(MenuWebService::class);
$currentRoute = request()->route()->getName();
$user = auth()->user();

Inertia::share([
    'menu' => $menuService->getMenu($currentRoute, $user),
    'favoriteMenuItems' => $menuService->getFavoriteMenuItems($user),
]);
```

### 2. Include SidebarMenu Component

In your main layout file (e.g., `resources/js/Pages/Layout.vue`):

```vue
<template>
  <div class="flex">
    <SidebarMenu :logo-url="'/images/logo.png'" :logo-alt="'My Logo'" />
    <main class="flex-1">
      <slot />
    </main>
  </div>
</template>

<script setup>
import SidebarMenu from '@/Components/SidebarMenu/SidebarMenu.vue'
</script>
```

### 3. Set Up Menu Groups and Items

You can create menu groups and items via database seeders or directly in the database:

```php
use JacquesTredoux\VueSidebarMenu\Models\MenuGroup;
use JacquesTredoux\VueSidebarMenu\Models\MenuItem;

// Create a menu group
$group = MenuGroup::create([
    'key' => 'dashboard',
    'label' => 'Dashboard',
    'icon' => 'heroicon-o-home',
    'sort_order' => 1,
    'is_active' => true,
]);

// Create a menu item
MenuItem::create([
    'menu_group_id' => $group->id,
    'key' => 'main-dashboard',
    'label' => 'Main Dashboard',
    'icon' => 'heroicon-o-chart-bar',
    'route' => 'vue.dashboard',
    'permission_name' => 'view_any_dashboard', // Optional: require permission
    'sort_order' => 1,
    'is_active' => true,
]);
```

### 4. (Optional) Add Favorite Menu Items Support

Add the relationship to your User model:

```php
use JacquesTredoux\VueSidebarMenu\Models\UserFavoriteMenuItem;

public function favoriteMenuItems()
{
    return $this->hasMany(UserFavoriteMenuItem::class);
}

public function getFavoriteMenuKeys()
{
    return $this->favoriteMenuItems()->pluck('menu_key')->toArray();
}
```

## Menu Item Structure

### MenuGroup Fields

- `key`: Unique identifier (e.g., 'dashboard', 'finance')
- `label`: Display name
- `icon`: Heroicon class name (e.g., 'heroicon-o-home')
- `sort_order`: Display order
- `is_active`: Whether the group is active

### MenuItem Fields

- `menu_group_id`: Foreign key to menu_groups
- `parent_id`: Foreign key to menu_items (for nested items, nullable)
- `key`: Unique identifier within the group
- `label`: Display name
- `icon`: Heroicon class name
- `url`: Direct URL (fallback, nullable)
- `route`: Laravel route name (nullable)
- `permission_name`: Required permission name (nullable)
- `sort_order`: Display order
- `is_active`: Whether the item is active

## Permission System

The package supports permission-based menu item visibility. It works with:

- Spatie Laravel Permission (via `getAllPermissions()` method)
- Custom permission systems (via `permissions()` relationship)

Permission names are automatically generated from route names:
- Route: `vue.roles.index` → Permissions: `view_any_role`, `view_any_role::*`

## Customization

### Logo

Pass the logo URL and alt text as props:

```vue
<SidebarMenu 
  logo-url="/images/logo.png" 
  logo-alt="Company Logo" 
/>
```

### Styling

The component uses Tailwind CSS classes. You can customize colors by modifying the classes in the published Vue components.

## License

MIT

## Support

For issues and questions, please open an issue on GitHub.

# vue-sidebar-menu
