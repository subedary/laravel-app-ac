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
            <label class="font-weight-bold">Filter By Department</label>
            <div class="input-group">
              <select id="moduleFilter" class="form-select ">
                <option value=""  >Choose a department...</option>
                      @if(isset($departments))
                          @foreach($departments as $id => $name)
                              <option value="{{ $id }}" @if(isset($role) && $role->department_id == $id) selected @endif>
                                  {{ $name }}
                              </option>
                          @endforeach
                      @endif
              </select>
            </div>
          </div>

          <div class="col-md-2">
            <a href="{{ route('masterapp.roles.index') }}" class="btn btn-secondary" id="clearFiltersBtn">Clear All</a>
          </div>

        </div>

      </form>
    </div>
    <!-- Search filter -->
