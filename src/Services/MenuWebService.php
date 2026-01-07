<?php

namespace JacquesTredoux\VueSidebarMenu\Services;

use JacquesTredoux\VueSidebarMenu\Models\MenuGroup;
use JacquesTredoux\VueSidebarMenu\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuWebService
{
    /**
     * Cache user permissions to avoid repeated queries
     */
    private ?array $cachedUserPermissions = null;

    /**
     * Track if debug has been logged to avoid repeated logging
     */
    private bool $debugLogged = false;

    public function getMenu(?string $currentRoute = null, $user = null): array
    {
        // Load user permissions once at the start to avoid repeated queries
        if ($user && $this->cachedUserPermissions === null) {
            $this->cachedUserPermissions = $this->getUserPermissionsList($user);
        }

        // Get menu from database instead of hardcoded array
        $menu = $this->getMenuFromDatabase($user);

        if ($currentRoute) {
            $this->markActiveItems($menu, $currentRoute);
        }

        if ($user) {
            $favoriteKeys = $this->getFavoriteMenuKeys($user);
            $this->markFavoritedItems($menu, $favoriteKeys);
        }

        // Clear cache after use to avoid stale data
        $this->cachedUserPermissions = null;
        $this->debugLogged = false;

        return $menu;
    }

    public function getFavoriteMenuItems($user): array
    {
        if (! $user) {
            return [];
        }

        // Load user permissions once at the start to avoid repeated queries
        if ($this->cachedUserPermissions === null) {
            $this->cachedUserPermissions = $this->getUserPermissionsList($user);
        }

        // Use database menu instead of hardcoded array
        $menu = $this->getMenuFromDatabase($user);
        $favoriteMenuItems = $this->getUserFavoriteMenuItems($user);
        $result = [];

        foreach ($favoriteMenuItems as $favorite) {
            $menuItem = $this->findMenuItemByKey($menu, $favorite->menu_key);
            if ($menuItem && $this->canAccessMenuItem($menuItem, $user)) {
                $menuItem['menu_key'] = $favorite->menu_key;
                $menuItem['order'] = $favorite->order;
                $result[] = $menuItem;
            }
        }

        // Clear cache after use to avoid stale data
        $this->cachedUserPermissions = null;
        $this->debugLogged = false;

        return $result;
    }

    /**
     * Get favorite menu keys for a user
     */
    private function getFavoriteMenuKeys($user): array
    {
        if (! $user) {
            return [];
        }

        // Check if user has favoriteMenuItems relationship
        if (method_exists($user, 'favoriteMenuItems')) {
            return $user->favoriteMenuItems()->pluck('menu_key')->toArray();
        }

        // Fallback: use UserFavoriteMenuItem model directly
        $userModel = config('auth.providers.users.model', 'App\Models\User');
        if ($user instanceof $userModel) {
            return \JacquesTredoux\VueSidebarMenu\Models\UserFavoriteMenuItem::where('user_id', $user->id)
                ->pluck('menu_key')
                ->toArray();
        }

        return [];
    }

    /**
     * Get user favorite menu items
     */
    private function getUserFavoriteMenuItems($user)
    {
        if (! $user) {
            return collect();
        }

        // Check if user has favoriteMenuItems relationship
        if (method_exists($user, 'favoriteMenuItems')) {
            return $user->favoriteMenuItems()->orderBy('order')->get();
        }

        // Fallback: use UserFavoriteMenuItem model directly
        $userModel = config('auth.providers.users.model', 'App\Models\User');
        if ($user instanceof $userModel) {
            return \JacquesTredoux\VueSidebarMenu\Models\UserFavoriteMenuItem::where('user_id', $user->id)
                ->orderBy('order')
                ->get();
        }

        return collect();
    }

    private function findMenuItemByKey(array $menu, string $key): ?array
    {
        foreach ($menu as $menuKey => $item) {
            if ($menuKey === $key) {
                return $item;
            }

            if (isset($item['children'])) {
                $found = $this->findMenuItemByKey($item['children'], $key);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    private function markFavoritedItems(array &$menu, array $favoriteKeys): void
    {
        foreach ($menu as $key => &$item) {
            if (in_array($key, $favoriteKeys)) {
                $item['is_favorited'] = true;
            }

            if (isset($item['children'])) {
                $this->markFavoritedItems($item['children'], $favoriteKeys);
            }
        }
    }

    private function markActiveItems(array &$menu, string $currentRoute): void
    {
        $currentUrl = request()->fullUrl();

        foreach ($menu as $key => &$item) {
            $item['active'] = false;

            // Check if URLs match exactly (including query parameters)
            if (isset($item['url']) && $this->isUrlMatch($item['url'], $currentUrl)) {
                $item['active'] = true;
            }
            // Fallback to route matching if no exact URL match
            elseif (isset($item['route']) && $this->isRouteMatch($item['route'], $currentRoute)) {
                // Only mark as active if no query parameters in current URL
                // This prevents "All Applications" from being active when filtered
                if (! request()->hasAny(['subscribable_type', 'progress', 'assigned_to_me'])) {
                    $item['active'] = true;
                }
            }

            if (isset($item['children'])) {
                $this->markActiveItems($item['children'], $currentRoute);

                $hasActiveChild = $this->hasActiveChild($item['children']);
                if ($hasActiveChild) {
                    $item['active'] = true;
                }
            }
        }
    }

    private function isUrlMatch(string $menuUrl, string $currentUrl): bool
    {
        // Normalize URLs for comparison
        $menuUrl = rtrim($menuUrl, '?');
        $currentUrl = rtrim($currentUrl, '?');

        return $menuUrl === $currentUrl;
    }

    private function isRouteMatch(string $menuRoute, string $currentRoute): bool
    {
        if ($menuRoute === $currentRoute) {
            return true;
        }

        $menuRouteParts = explode('.', $menuRoute);
        $currentRouteParts = explode('.', $currentRoute);

        if (count($menuRouteParts) < count($currentRouteParts)) {
            $menuRoutePrefix = implode('.', $menuRouteParts);
            $currentRoutePrefix = implode('.', array_slice($currentRouteParts, 0, count($menuRouteParts)));

            if ($menuRoutePrefix === $currentRoutePrefix) {
                return true;
            }
        }

        return false;
    }

    private function hasActiveChild(array $children): bool
    {
        foreach ($children as $child) {
            if (isset($child['active']) && $child['active']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter menu items based on user permissions
     */
    private function filterMenuByPermissions(array $menu, $user): array
    {
        $filtered = [];

        foreach ($menu as $key => $item) {
            // Check if user can access this item
            if (! $this->canAccessMenuItem($item, $user)) {
                continue;
            }

            // If item has children, filter them recursively
            if (isset($item['children']) && is_array($item['children'])) {
                $filteredChildren = $this->filterMenuByPermissions($item['children'], $user);

                // Only include item if it has visible children or no route (group header)
                if (empty($filteredChildren) && isset($item['route']) && $item['route'] !== null) {
                    continue;
                }

                $item['children'] = $filteredChildren;
            }

            $filtered[$key] = $item;
        }

        return $filtered;
    }

    /**
     * Check if user can access a menu item based on permissions
     */
    private function canAccessMenuItem(array $item, $user): bool
    {
        // If no route specified, allow access (for group headers)
        if (! isset($item['route']) || $item['route'] === null) {
            return true;
        }

        // Check if user is a Member (members don't have admin permissions)
        // This is application-specific, can be customized
        $memberModel = config('vue-sidebar-menu.member_model', null);
        if ($memberModel && $user instanceof $memberModel) {
            return false;
        }

        // Check if user implements Authorizable
        if (! $user instanceof \Illuminate\Contracts\Auth\Access\Authorizable) {
            return false;
        }

        $route = $item['route'];

        // Special cases that don't follow standard resource pattern
        if ($this->isSpecialRoute($route)) {
            return $this->canAccessSpecialRoute($route, $user);
        }

        // Standard resource routes: vue.{resource}.index
        return $this->canAccessRoute($route, $user);
    }

    /**
     * Get user permissions list (cached for performance)
     */
    private function getUserPermissionsList($user)
    {
        // Use cached permissions if available, otherwise load once
        // Use same cache duration logic as Navigation for consistency
        $cacheTime = config('vue-sidebar-menu.cache_time', config('app.debug') ? 60 * 5 : 60 * 60);
        return Cache::remember('user_permissions_'.$user->id, $cacheTime, function () use ($user) {
            // Check if user has getAllPermissions method (Spatie Permission)
            if (method_exists($user, 'getAllPermissions')) {
                return $user->getAllPermissions()->pluck('name')->toArray();
            }

            // Fallback: check if user has permissions relationship
            if (method_exists($user, 'permissions')) {
                return $user->permissions()->pluck('name')->toArray();
            }

            return [];
        });
    }

    /**
     * Check if user has a specific permission (using cached permissions array)
     */
    private function userHasPermission($user, string $permission): bool
    {
        // Ensure permissions are loaded
        if ($this->cachedUserPermissions === null && $user) {
            $this->cachedUserPermissions = $this->getUserPermissionsList($user);
        }

        if ($this->cachedUserPermissions === null) {
            return false;
        }

        return in_array($permission, $this->cachedUserPermissions, true);
    }

    /**
     * Check if user can access a standard resource route
     */
    private function canAccessRoute(?string $route, $user): bool
    {
        if (! $route) {
            return true;
        }

        // Extract resource name from route (e.g., vue.roles.index -> roles)
        $routeParts = explode('.', $route);
        if (count($routeParts) < 3 || $routeParts[0] !== 'vue') {
            // Not a standard vue-admin route, allow by default
            return true;
        }

        $resourceName = $routeParts[1];

        // Convert resource name to model name (singular)
        // e.g., roles -> Role, application-decline-reasons -> ApplicationDeclineReason
        // proforma-invoices -> ProformaInvoices -> ProformaInvoice
        $modelName = $this->routeToModelName($resourceName);

        // Get both singular and plural snake_case versions
        // Singular: ProformaInvoice -> proforma_invoice
        // Plural: proforma-invoices -> ProformaInvoices -> proforma_invoices
        $snakeCaseSingular = \Illuminate\Support\Str::snake($modelName);
        $words = explode('-', $resourceName);
        $pascalCasePlural = implode('', array_map('ucfirst', $words));
        $snakeCasePlural = \Illuminate\Support\Str::snake($pascalCasePlural);

        // Generate permission variations for both singular and plural
        // Filament uses :: instead of _ for multi-word model names
        $permissions = array_merge(
            $this->generatePermissionVariations($snakeCaseSingular),
            $this->generatePermissionVariations($snakeCasePlural)
        );

        // Debug logging when APP_DEBUG is enabled (only log first occurrence per request)
        if (config('app.debug') && ! $this->debugLogged) {
            $this->debugLogged = true;
            // Ensure permissions are loaded
            if ($this->cachedUserPermissions === null) {
                $this->cachedUserPermissions = $this->getUserPermissionsList($user);
            }
            $userPermissions = $this->cachedUserPermissions;
            $userRoles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];

            // Only load role details if roles are already loaded (avoid N+1)
            $roleDetails = [];
            if (method_exists($user, 'relationLoaded') && $user->relationLoaded('roles')) {
                foreach ($user->roles as $role) {
                    $roleDetails[] = [
                        'id' => $role->id,
                        'name' => $role->name,
                        'permissions' => method_exists($role, 'relationLoaded') && $role->relationLoaded('permissions')
                            ? $role->permissions->pluck('name')->toArray()
                            : [],
                        'permissions_count' => method_exists($role, 'relationLoaded') && $role->relationLoaded('permissions')
                            ? $role->permissions->count()
                            : 0,
                    ];
                }
            }

            \Illuminate\Support\Facades\Log::debug('MenuWebService: Permission check optimization', [
                'user_id' => $user->id ?? null,
                'user_email' => $user->email ?? null,
                'user_roles' => $userRoles,
                'role_details' => $roleDetails,
                'user_permissions_count' => count($userPermissions),
                'permissions_cached' => $this->cachedUserPermissions !== null,
            ]);
        }

        // Check if user has any of these permissions using cached list
        foreach ($permissions as $permission) {
            if ($this->userHasPermission($user, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate permission variations for a given snake_case string
     * Returns both underscore and double-colon formats
     * e.g., proforma_invoice -> ['view_any_proforma_invoice', 'view_any_proforma::invoice']
     */
    private function generatePermissionVariations(string $snakeCase): array
    {
        return [
            'view_any_'.$snakeCase, // underscore format
            'view_any_'.str_replace('_', '::', $snakeCase), // double-colon format (Filament)
        ];
    }

    /**
     * Convert route resource name to model name
     * e.g., roles -> Role, application-decline-reasons -> ApplicationDeclineReason
     */
    private function routeToModelName(string $resourceName): string
    {
        // Convert kebab-case to PascalCase
        $words = explode('-', $resourceName);
        $pascalCase = implode('', array_map('ucfirst', $words));

        // Handle plural to singular conversion for common cases
        $singular = $this->pluralToSingular($pascalCase);

        return $singular;
    }

    /**
     * Convert plural model name to singular
     */
    private function pluralToSingular(string $name): string
    {
        // Handle words ending in 'ies' -> 'y' (e.g., Countries -> Country)
        if (preg_match('/ies$/i', $name)) {
            return preg_replace('/ies$/i', 'y', $name);
        }

        // Handle words ending in 'ses' -> 's' (e.g., Classes -> Class)
        if (preg_match('/ses$/i', $name)) {
            return preg_replace('/ses$/i', 's', $name);
        }

        // Handle words ending in 'ches' -> 'ch' (e.g., Branches -> Branch)
        if (preg_match('/ches$/i', $name)) {
            return preg_replace('/ches$/i', 'ch', $name);
        }

        // Handle words ending in 'shes' -> 'sh' (e.g., Dishes -> Dish)
        if (preg_match('/shes$/i', $name)) {
            return preg_replace('/shes$/i', 'sh', $name);
        }

        // Handle words ending in 'xes' -> 'x' (e.g., Boxes -> Box)
        if (preg_match('/xes$/i', $name)) {
            return preg_replace('/xes$/i', 'x', $name);
        }

        // Handle words ending in 'zes' -> 'z' (e.g., Quizzes -> Quiz)
        if (preg_match('/zes$/i', $name)) {
            return preg_replace('/zes$/i', 'z', $name);
        }

        // Default: remove 's' at the end if present
        if (substr($name, -1) === 's' && strlen($name) > 1) {
            return substr($name, 0, -1);
        }

        return $name;
    }

    /**
     * Check if route is a special case (dashboards, reports, etc.)
     */
    private function isSpecialRoute(string $route): bool
    {
        $specialPrefixes = [
            'vue.dashboard',
            'vue.dashboards.',
            'vue.reports.',
        ];

        foreach ($specialPrefixes as $prefix) {
            if (str_starts_with($route, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access special routes (dashboards, reports)
     */
    private function canAccessSpecialRoute(string $route, $user): bool
    {
        // Dashboard routes - allow if user is authenticated
        if (str_starts_with($route, 'vue.dashboard') || str_starts_with($route, 'vue.dashboards.')) {
            return true;
        }

        // Report routes - check for specific report permissions
        if (str_starts_with($route, 'vue.reports.')) {
            // Extract report name from route
            // e.g., vue.reports.sars.cpd-verification -> sars_cpd_verification
            $routeParts = explode('.', $route);
            if (count($routeParts) >= 3) {
                $reportParts = array_slice($routeParts, 2);
                $reportName = implode('_', $reportParts);
                $permission = 'view_any_report_'.$reportName;

                // Try both formats
                $permissionFilament = 'view_any_report::'.str_replace('_', '::', $reportName);

                return $this->userHasPermission($user, $permission) || $this->userHasPermission($user, $permissionFilament);
            }

            // Default: allow if user can view any report
            return $this->userHasPermission($user, 'view_any_report');
        }

        // Default: allow access for unknown special routes
        return true;
    }

    /**
     * Get menu from database, grouped by MenuGroup and filtered by user permissions
     */
    private function getMenuFromDatabase($user = null): array
    {
        // Load user permissions if not already loaded
        if ($user && $this->cachedUserPermissions === null) {
            $this->cachedUserPermissions = $this->getUserPermissionsList($user);
        }

        // Query active MenuGroups ordered by sort_order
        $menuGroups = MenuGroup::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $menu = [];

        foreach ($menuGroups as $group) {
            // Build permission filter for MenuItems
            $menuItemsQuery = MenuItem::where('menu_group_id', $group->id)
                ->where('is_active', true)
                ->where(function ($query) use ($user) {
                    // Show items with no permission requirement (accessible to all)
                    $query->whereNull('permission_name');

                    // OR show items where user has the required permission
                    if ($user && $this->cachedUserPermissions !== null && ! empty($this->cachedUserPermissions)) {
                        $query->orWhereIn('permission_name', $this->cachedUserPermissions);
                    }
                })
                ->orderBy('sort_order')
                ->get();

            // Build nested structure for menu items
            $children = $this->buildMenuItemTree($menuItemsQuery, $user);

            // Only include group if it has accessible children
            if (! empty($children)) {
                $menu[$group->key] = [
                    'label' => $group->label,
                    'url' => '#',
                    'icon' => $group->icon,
                    'route' => null,
                    'children' => $children,
                ];
            }
        }

        return $menu;
    }

    /**
     * Build nested tree structure for menu items, handling parent_id relationships
     */
    private function buildMenuItemTree($menuItems, $user = null): array
    {
        $tree = [];
        $itemsByParent = [];

        // Group items by parent_id
        foreach ($menuItems as $item) {
            $parentId = $item->parent_id ?? 'root';
            if (! isset($itemsByParent[$parentId])) {
                $itemsByParent[$parentId] = [];
            }
            $itemsByParent[$parentId][] = $item;
        }

        // Build tree starting from root items (parent_id is null)
        if (isset($itemsByParent['root'])) {
            foreach ($itemsByParent['root'] as $item) {
                $menuItemData = $this->transformMenuItemToArray($item);

                // Recursively add children
                if (isset($itemsByParent[$item->id])) {
                    $menuItemData['children'] = $this->buildChildrenTree($itemsByParent[$item->id], $itemsByParent, $user);
                }

                $tree[$item->key] = $menuItemData;
            }
        }

        return $tree;
    }

    /**
     * Recursively build children tree for nested menu items
     */
    private function buildChildrenTree($children, &$itemsByParent, $user = null): array
    {
        $tree = [];

        foreach ($children as $item) {
            $menuItemData = $this->transformMenuItemToArray($item);

            // Recursively add nested children
            if (isset($itemsByParent[$item->id])) {
                $menuItemData['children'] = $this->buildChildrenTree($itemsByParent[$item->id], $itemsByParent, $user);
            }

            $tree[$item->key] = $menuItemData;
        }

        return $tree;
    }

    /**
     * Transform MenuItem model to array format matching getMenuArray() structure
     */
    private function transformMenuItemToArray(MenuItem $item): array
    {
        $data = [
            'label' => $item->label,
            'icon' => $item->icon,
            'route' => $item->route,
        ];

        // Set URL - use route() helper if route exists, otherwise use stored URL
        if ($item->route && \Illuminate\Support\Facades\Route::has($item->route)) {
            $data['url'] = route($item->route);
        } else {
            $data['url'] = $item->url ?? '#';
        }

        return $data;
    }
}

