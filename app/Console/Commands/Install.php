<?php

namespace App\Console\Commands;

use App\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use App\Repositories\RoleRepository;
use App\Repositories\LocaleRepository;
use Spatie\Permission\Models\Permission;
use App\Repositories\PermissionRepository;

class Install extends Command
{
    /**
     * @var CategoryRepository
     */
    protected $category;

    /**
     * @var LocaleRepository
     */
    protected $locale;

    /**
     * @var RoleRepository
     */
    protected $role;

    /**
     * @var PermissionRepository
     */
    protected $permission;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installation';

    /**
     * Create a new command instance.
     *
     * @param CategoryRepository $category
     * @param LocaleRepository $locale
     * @param RoleRepository $role
     * @param PermissionRepository $permission
     */
    public function __construct(CategoryRepository $category, LocaleRepository $locale, RoleRepository $role, PermissionRepository $permission)
    {
        $this->category = $category;
        $this->locale = $locale;
        $this->role = $role;
        $this->permission = $permission;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle()
    {
        $this->createCategories();
        $this->createRoles();
        $this->createPermissions();
        $this->assignPermissions();
        $this->createLocals();

        $this->info('Installation Completed Successfully');
    }

    /**
     * Initialize default roles.
     */
    protected function createCategories()
    {
        $categories = $this->category->listName();
        foreach (config('system.default_category') as $key => $value) {
            if (!in_array($value, $categories)) {
                Category::create([
                    'name' => $value,
                    'slug' => str_slug($value),
                ]);
            }
        }
    }

    /**
     * Initialize default roles.
     */
    protected function createRoles()
    {
        $roles = $this->role->listName();
        foreach (config('system.default_role') as $key => $value) {
            if (!in_array($value, $roles)) {
                Role::create(['name' => $value]);
            }
        }
    }

    /**
     * Initialize default permissions.
     */
    protected function createPermissions()
    {
        $permissions = $this->permission->listName();
        foreach (config('system.default_permission') as $value) {
            if (!in_array($value, $permissions)) {
                Permission::create(['name' => $value]);
            }
        }
    }

    /**
     * Assign default permission to admin roles.
     */
    protected function assignPermissions()
    {
        $role = Role::whereName(config('system.default_role.admin'))->first();
        $role->syncPermissions(config('system.default_permission'));
    }

    /**
     * Initialize default locales.
     */
    protected function createLocals()
    {
        $this->locale->firstOrCreate(['locale' => 'en', 'name' => 'English']);
        $this->locale->firstOrCreate(['locale' => 'ru', 'name' => 'Russian']);
    }
}
