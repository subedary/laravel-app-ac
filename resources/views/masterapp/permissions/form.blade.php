<div class="mb-3">
    <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="name" name="name" value="{{ $permission->name ?? '' }}" required>
    <!-- The invalid-feedback div will be added by JavaScript if there's an error -->
</div>

<div class="mb-3">
    <label for="name" class="form-label">Assign to Module</label>
    <select name="module_id" id="module_id" class="form-control" required>
        <option value="">-- Select a Module --</option>
          @if(isset($modules))
            @foreach($modules as $id => $name)
            <option value="{{ $id }}" {{ isset($permission) && ($permission->module_id== $id) ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        @endif
    </select>
</div>

<div class="mb-3">
    <label for="slug" class="form-label">Display Name</label>
    <input type="text" class="form-control" id="display_name" name="display_name" value="{{ $permission->display_name ?? '' }}" required>

</div>
