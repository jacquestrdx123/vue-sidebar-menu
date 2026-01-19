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

2. Install Heroicons (required dependency unless skipping Vue publish):

```bash
npm install @heroicons/vue
```

3. Run the install command:

```bash
php artisan vue-sidebar-menu:install
```

If you want to skip publishing Vue components and JS utilities:

```bash
php artisan vue-sidebar-menu:install --no-vue
```

This will:

- Publish migrations to `database/migrations/`
- Publish Vue components to `resources/js/Components/SidebarMenu/` (unless `--no-vue`)
- Publish icon mapper utility to `resources/js/utils/iconMapper.js` (unless `--no-vue`)
- Publish config file to `config/vue-sidebar-menu.php`

4. Run migrations:

```bash
php artisan migrate
```

5. (Optional) Remove Material Icons from your CSS if no longer needed:

Remove the Material Icons import from your `resources/css/app.css`:

```css
/* Remove this line if you're no longer using Material Icons */
@import url("https://fonts.googleapis.com/css2?family=Material+Icons");
```

## Configuration

The config file is published to `config/vue-sidebar-menu.php`. You can customize:

- `cache_time`: Cache duration for user permissions (defaults to 5 minutes in debug, 1 hour in production)
- `member_model`: Member model class name (if you have a separate Member model)
- `user_model`: User model class name (defaults to Laravel auth provider)

## Usage

### 1. Share Menu Data via Inertia Middleware

Update your `app/Http/Middleware/HandleInertiaRequests.php`:

```php
use JacquesTredoux\VueSidebarMenu\Services\MenuWebService;
use Inertia\Inertia;

class HandleInertiaRequests extends Middleware
{
    public $menuService;

    public function __construct(RootTemplateProvider $rootTemplateProvider)
    {
        $this->rootTemplateProvider = $rootTemplateProvider;
        $this->menuService = app(MenuWebService::class);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'menu' => $this->menuService->getMenu($request->route()?->getName(), $user),
            'favoriteMenuItems' => $this->menuService->getFavoriteMenuItems($user),
        ];
    }
}
```

### 2. Include SidebarMenu Component in Your Layout

In your authenticated layout file (e.g., `resources/js/Layouts/Authenticated.vue`):

```vue
<template>
  <div class="flex">
    <SidebarMenu :logo-url="'/images/logo.png'" :logo-alt="'My Logo'" />
    <main
      :class="[
        'flex-1 transition-all duration-300',
        isSidebarCollapsed ? 'ml-16' : 'ml-[250px]',
      ]"
    >
      <slot />
    </main>
  </div>
</template>

<script setup>
import { useSidebar } from "@/Components/SidebarMenu/composables/useSidebar.js";
import SidebarMenu from "@/Components/SidebarMenu/SidebarMenu.vue";

const { isCollapsed: isSidebarCollapsed } = useSidebar();
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
- `icon`: Icon name (supports both Material Design and Heroicons - see Icon Support below)
- `sort_order`: Display order
- `is_active`: Whether the group is active

### MenuItem Fields

- `menu_group_id`: Foreign key to menu_groups
- `parent_id`: Foreign key to menu_items (for nested items, nullable)
- `key`: Unique identifier within the group
- `label`: Display name
- `icon`: Icon name (supports both Material Design and Heroicons - see Icon Support below)
- `url`: Direct URL (fallback, nullable)
- `route`: Laravel route name (nullable)
- `permission_name`: Required permission name (nullable)
- `sort_order`: Display order
- `is_active`: Whether the item is active

## Icon Support

The package uses Heroicons by default, but includes backward compatibility with Material Design icons through the `iconMapper` utility.

### Supported Icon Formats

1. **Heroicons** (recommended):

   - Simple name: `home`, `user`, `settings`
   - Full name: `heroicon-o-home`, `heroicon-o-user`

2. **Material Design Icons** (backward compatible):
   - Material icon name: `home`, `dashboard`, `settings`
   - MDI format: `mdi-home`, `mdi-dashboard`

The icon mapper automatically converts Material Design icon names to their Heroicon equivalents. Common mappings include:

- `home` / `mdi-home` → `HomeIcon`
- `dashboard` / `mdi-view-dashboard` → `ChartBarIcon`
- `settings` / `mdi-cog` → `Cog6ToothIcon`
- `user` / `mdi-account` → `UserIcon`
- And many more...

See `resources/js/utils/iconMapper.js` for the complete mapping list.

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
<SidebarMenu logo-url="/images/logo.png" logo-alt="Company Logo" />
```

### Styling

The component uses Tailwind CSS classes. You can customize colors by modifying the classes in the published Vue components.

### Icon Customization

The icon mapper utility (`resources/js/utils/iconMapper.js`) supports both Material Design and Heroicon formats. You can:

1. Use Heroicons directly (recommended):

```php
MenuItem::create([
    'icon' => 'home',  // or 'heroicon-o-home'
    // ...
]);
```

2. Continue using Material Design icons (backward compatible):

```php
MenuItem::create([
    'icon' => 'mdi-home',  // Automatically converted to HomeIcon
    // ...
]);
```

3. Extend the icon mapper with custom mappings in your application's `resources/js/utils/iconMapper.js` file.

## License

MIT

## Key Features

1. **Database-Driven Menu**: Menu structure is stored in database tables instead of hardcoded arrays
2. **Permission-Based Filtering**: Menu items are filtered based on user permissions
3. **Favorite Menu Items**: Users can favorite menu items for quick access
4. **Search Functionality**: Built-in search to filter menu items
5. **Collapsible Sidebar**: Sidebar can be collapsed/expanded with state persistence in localStorage
6. **Active Route Highlighting**: Current route is automatically highlighted in the menu
7. **Nested Menu Support**: Supports multi-level nested menu items
8. **Heroicons Integration**: All icons use Heroicons (with Material Design backward compatibility)
9. **Responsive Design**: Automatically collapses on mobile devices

## Migration from Material Icons

If you're migrating from a Material Icons-based menu system:

1. The icon mapper automatically handles conversion of Material Design icon names to Heroicons
2. Your existing menu items in the database will continue to work
3. No need to update icon names in the database - the package handles the conversion automatically
4. You can optionally remove Material Icons from your CSS after migration

## Troubleshooting

### Icons Not Displaying

- Ensure `@heroicons/vue` is installed: `npm install @heroicons/vue`
- Check that the icon mapper utility is published: `resources/js/utils/iconMapper.js`
- Verify icon names in your database match supported formats

### Sidebar Not Showing

- Ensure the `SidebarMenu` component is imported and included in your layout
- Check that menu data is shared via Inertia middleware
- Verify the component path is correct: `@/Components/SidebarMenu/SidebarMenu.vue`

### Menu Items Not Filtering by Permissions

- Verify user permissions are being loaded correctly
- Check that `permission_name` is set correctly in menu items
- Ensure your User model implements `Authorizable` interface (Laravel default)

## Support

For issues and questions, please open an issue on GitHub.
