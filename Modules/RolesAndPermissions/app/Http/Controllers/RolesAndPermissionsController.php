<?php

namespace Modules\RolesAndPermissions\Http\Controllers;

use App\Constants\TableConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTableSettingsRequest;
use App\Models\TableSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\RolesAndPermissions\Models\Role;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $savedSettings = $this->_getTableSettingsForModel(Role::class);

        $limits = [5, 10, 20, 50, 100];
        $limit = $request->get('limit', $savedSettings->limit ?? 10);
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'ASC');

        // Retrieve all columns and visible columns
        [$allColumns, $visibleColumns] = $this->_getColumnsForTable();

        // Exclude '' from the database query
        $excludedColumns = ['permissions'];
        $queryColumns = array_diff($visibleColumns, $excludedColumns);

        $roles = Role::search(
            keyword: $request->q,
            columns: $queryColumns
        )
        ->sort(
            sort_by: $sortBy,
            sort_order: $sortOrder
        );

        $roles = $roles->paginate($limit)->through(fn($role) => $role->setAttribute('permissions', $role->permissions->pluck('name')->toArray()));
    
        return view('rolesandpermissions::index', [
            'title' => 'Role and Permission List',
            'columns' => $allColumns,
            'visibleColumns' => $visibleColumns,
            'excludedSortColumns' => $excludedColumns,
            'limits' => $limits,
            'roles' => $roles,
            'savedSettings' => $savedSettings
        ]);        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('module');

        return view('rolesandpermissions::create', [
            'title' => 'Create Role and Permission',
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'role_name' => 'required|string|max:255',
                'permissions' => 'required|array',
                'permissions.*' => 'integer|exists:permissions,id',
            ]);

            $role = Role::create([
                'name' => $request->input('role_name'),
                'guard_name' => 'web',
            ]);

            $role->permissions()->attach($validated['permissions']);

            return redirect()->route('roles.and.permissions.index')->with('success', 'Role and permission created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating role and permissions: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'An error occurred while creating the role. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role_and_permission)
    {
        $permissions = Permission::all()->groupBy('module');
        $role = Role::findOrFail($role_and_permission->id);
        $rolePermissions = $role_and_permission->permissions->pluck('id')->toArray();

        return view('rolesandpermissions::edit', [
            'title' => 'Edit Role and Permission',
            'permissions' => $permissions,
            'role' => $role,
            'rolePermissions' => $rolePermissions
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role_and_permission)
    {
        try {
            $request->validate([
                'role_name' => 'required|string|max:255',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,id',
            ]);

            $role_and_permission->update(['name' => $request->input('role_name')]);
            $role_and_permission->permissions()->sync($request->input('permissions', []));

            return redirect()->route('roles.and.permissions.index')->with('success', 'Role and permission updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating role and permissions: ' . $e->getMessage(), [
                'exception' => $e,
                'role_id' => $role_and_permission->id,
                'request' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'An error occurred while updating the role. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role_and_permission)
    {
        try {
            $role_and_permission->delete();

            return redirect()->route('roles.and.permissions.index')->with('success', 'Role and permission deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting role and permissions: ' . $e->getMessage(), [
                'exception' => $e,
                'role_id' => $role_and_permission->id,
            ]);

            return back()->withErrors(['error' => 'An error occurred while deleting the role. Please try again later.']);
        }
    }

    /**
     * Save table settings for the user.
     */
    public function saveTableSettings(StoreTableSettingsRequest $request)
    {
        try {
            $modelClass = Role::class;
            $modelInstance = app($modelClass)->newInstance();
    
            $tableName = $modelInstance->getTable();
            $modelName = get_class($modelInstance);
    
            $columns = $request->input('columns', []);
            $showNumbering = $request->has('show_numbering') ? true : false;
    
            TableSettings::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'table_name' => $tableName,
                    'model_name' => $modelName,
                ],
                [
                    'visible_columns' => json_encode($columns),
                    'limit' => $request->input('limit', 10),
                    'show_numbering' => $showNumbering,
                ]
            );
    
            return redirect()->back()->with('success', 'Table settings saved successfully!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // Log the error and return a failure message
            Log::error('Error saving table settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save table settings.');
        }
    }

    /**
     * Get all columns and visible columns for the table.
     */
    private function _getColumnsForTable(): array
    {
        $allColumns = TableConstants::ROLE_AND_PERMISSION_TABLE_COLUMNS;

        // Use the reusable function to get table settings
        $tableSettings = $this->_getTableSettingsForModel(Role::class);

        // Extract visible columns or use default if not set
        $visibleColumns = $tableSettings->visible_columns ?? $allColumns;

        // Decode JSON if necessary
        $visibleColumns = is_string($visibleColumns) ? json_decode($visibleColumns, true) : $visibleColumns;

        return [$allColumns, $visibleColumns];
    }

    /**
     * Get table settings for a specific model and table.
     *
     * @param string $modelClass The fully qualified model class name.
     * @return mixed|null The table settings or null if not found.
     */
    private function _getTableSettingsForModel(string $modelClass)
    {
        // Create a new instance of the model to retrieve its table name
        $modelInstance = app($modelClass)->newInstance();
        $tableName = $modelInstance->getTable();

        // Check if Auth::user()->tableSettings is null
        $userTableSettings = Auth::user()->tableSettings;

        if (is_null($userTableSettings)) {
            return null;
        }

        // Retrieve user's table settings for the given model and table
        $tableSettings = $userTableSettings
            ->where('model_name', $modelClass)
            ->where('table_name', $tableName)
            ->first();

        return $tableSettings;
    }
}
