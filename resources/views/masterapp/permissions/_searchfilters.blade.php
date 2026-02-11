    <!-- Search filter -->
    <div class="filter-wrapper" id="filterWrapper" style="display: block;">
      <a href="#" class="close-filter-btn"  id="toggleFilterclear" title="Clear Filters & Close">
        &times;
      </a>
      <form id="filterForm">
        <div class="row align-items-end">
          <div class="col-md-3">
            <label class="font-weight-bold">Search</label>
            <div class="input-group">
              <input type="text" id="customSearchInput" class="form-control" placeholder="Search..">
            </div>
          </div>

         

          <div class="col-md-3">
            <label class="font-weight-bold">Filter By Modules</label>
            <div class="input-group">
              <select id="moduleFilter" class="form-select ">
                <option value=""> -All Modules-</option>
                @if(isset($modules))
                @foreach($modules as $module)
                <option value="{{ $module->name }}">{{ $module->name }}</option>
                @endforeach
                @endif
              </select>
            </div>
          </div>

          <div class="col-md-2">
            <a href="{{ route('masterapp.permissions.index') }}" class="btn btn-secondary" id="clearFiltersBtn">Clear All</a>
          </div>

        </div>

      </form>
    </div>
    <!-- Search filter -->
