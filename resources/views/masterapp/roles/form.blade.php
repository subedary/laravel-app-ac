<div class="mb-3">
    <!-- Role Name Input -->
    <div class="mb-3">
        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" value="{{ isset($role) ? $role->name : '' }}" class="form-control" placeholder="Enter role name" required>
    </div>


      <div class="mb-3">
        <label for="department_id" class="form-label">Department</label>
        <select id="department_id" name="department_id" class="form-select form-control">
            <option value="" @if(!isset($role) || empty($role->department_id)) selected @endif>Choose a department...</option>
            @if(isset($departments))
                @foreach($departments as $id => $name)
                    <option value="{{ $id }}" @if(isset($role) && $role->department_id == $id) selected @endif>
                        {{ $name }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>

    <!-- Master Checkboxes on the same line, but on opposite sides -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Left side: "Assign Permissions" -->
        <div>
            <label class="form-check-label fw-bold" for="checkAllPermissions">
                <b> Assign Permissions</b>
            </label>
        </div>

    </div>

  

    <!-- The container for grouped checkboxes remains the same -->
    <div id="permissions-container" style="max-height: 400px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 0.375rem; padding: 10px;">
        @if(isset($groupedPermissions))
        <?php //echo "<pre>";print_r($groupedPermissions); 
        ?>
        @foreach($groupedPermissions as $moduleName => $modulePermissions)

        <summary class="fw-bold text-white mb-2 p-1" style="background-color: #6FA5E8;"> <input class="parent" name="module[]" id="parent{{ $modulePermissions->first()->module_id }}" value="{{ $modulePermissions->first()->module_id }}" type="checkbox" onchange="selectAll(this);"> {{ $moduleName }}
            <input name="moduleid[]" value="{{ $modulePermissions->first()->module_id }}" type="hidden">
        </summary>


        <div class="row">
            @foreach($modulePermissions as $permission)
            <div class="col-md-6 mb-2">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox child" onchange="checkAllBox();" data-parent="parent{{ $modulePermissions->first()->module_id }}" type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->id }}"
                        @if(isset($role) && in_array($permission->id, $rolePermissions)) checked @endif >
                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                        {{ $permission->display_name ?? $permission->name }}
                    </label>
                </div>
            </div>
            @endforeach
        </div>

        @if (!$loop->last)
        <hr class="my-3">
        @endif
        @endforeach
        @endif
    </div>
</div>
