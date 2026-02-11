@extends('layouts.custom-admin')

@section('title', 'Activity Logs','bold')

@section('content')
<div class="mx-auto">

    <div class="flex items-center justify-end mb-6">

        <a href="{{ route('audit.export', request()->query()) }}"
           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm border">
            Export to Excel
        </a>
    </div>

<!-- Filter Toggle Button -->
<div class="items-center display:flex justify-start mb-6">
<button 
    onclick="toggleFilters()" 
    class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg  border">

    <!-- Filter Icon -->
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel-fill" viewBox="0 0 16 16">
  <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5z"/>
</svg>
    <span class="text-sm font-medium"> Filters</span>
</button>

<!-- COLUMN SELECTOR FILTER-STYLE BOX -->
<button 
    onclick="toggleColumnSelector()" 
    class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 
           text-gray-700 rounded-lg  border mt-2">
    <i class="fas fa-columns"></i>
    <span class="text-sm font-medium">Select Columns</span>
</button>
</div>



<!-- Filters -->
<form id="filterSection" method="GET" 
      action="{{ route('audit.index') }}"
      class="bg-white p-4 rounded-lg  mb-6 hidden">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div>
            <label class="text-sm font-medium text-gray-700">Event</label>
            <select name="event" class="w-full mt-1 p-2 border rounded">
                <option value="">All</option>
                @foreach($events as $ev)
                    <option value="{{ $ev }}" {{ request('event') == $ev ? 'selected' : '' }}>
                        {{ ucfirst($ev) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">Model</label>
            <select name="auditable_type" class="w-full mt-1 p-2 border rounded">
                <option value="">All</option>
                @foreach($auditableTypes as $type)
                    <option value="{{ $type }}" {{ request('auditable_type') == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">User</label>
            <select name="user_id" class="w-full mt-1 p-2 border rounded">
                <option value="">All</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">Date from → to</label>
            <div class="flex gap-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-1/2 p-2 border rounded">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-1/2 p-2 border rounded">
            </div>
        </div>

        <div class="md:col-span-4">
            <label class="text-sm font-medium text-gray-700">Search</label>
            <div class="flex gap-2 mt-1">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Search..."
                       class="flex-1 p-2 border rounded">

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                    Filter
                </button>

                <a href="{{ route('audit.index') }}" class="px-4 py-2 bg-gray-200 rounded">
                    Reset
                </a>
            </div>
        </div>

    </div>
</form>


    <!-- Filters -->
    {{-- <form method="GET" action="{{ route('audit.index') }}" class="bg-white p-4 rounded-lg  mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700">Event</label>
                <select name="event" class="w-full mt-1 p-2 border rounded">
                    <option value="">All</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev }}" {{ request('event')== $ev ? 'selected' : '' }}>
                            {{ ucfirst($ev) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Model</label>
                <select name="auditable_type" class="w-full mt-1 p-2 border rounded">
                    <option value="">All</option>
                    @foreach($auditableTypes as $type)
                        <option value="{{ $type }}" {{ request('auditable_type')== $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">User</label>
                <select name="user_id" class="w-full mt-1 p-2 border rounded">
                    <option value="">All</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id')== $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Date from → to</label>
                <div class="flex gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-1/2 p-2 border rounded">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-1/2 p-2 border rounded">
                </div>
            </div>

            <div class="md:col-span-4">
                <label class="text-sm font-medium text-gray-700">Search</label>
                <div class="flex gap-2 mt-1">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="search ID, old/new values, model..."
                           class="flex-1 p-2 border rounded">

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Filter</button>
                    <a href="{{ route('audit.index') }}" class="px-4 py-2 bg-gray-200 rounded">Reset</a>
                </div>
            </div>
        </div>
    </form> --}}

<!-- Column Selector Section -->
<div id="columnSelector" class="hidden bg-white p-4 border rounded-lg mt-2 ">
    <p class="font-medium mb-2">Choose Columns to Display:</p>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
        <label><input type="checkbox" class="column-toggle" value="when" checked> When</label>
        <label><input type="checkbox" class="column-toggle" value="user" checked> User</label>
        <label><input type="checkbox" class="column-toggle" value="events" checked> Events</label>
        <label><input type="checkbox" class="column-toggle" value="model" checked> Model</label>
        <label><input type="checkbox" class="column-toggle" value="activity"> Activity</label>
        <label><input type="checkbox" class="column-toggle" value="meta" checked> Meta</label>
    </div>
</div>

<br>

    <!-- Audits table -->

    <table class="min-w-full table-bordered">
    <thead class="bg-gray-50">
    <tr>
        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-sm font-semibold text-gray-700 uppercase tracking-wider">Sr. No.</th>

        <th class="when-col px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-sm font-semibold text-gray-700 uppercase tracking-wider">When</th>
        <th class="user-col px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-sm font-semibold text-gray-700 uppercase tracking-wider">User</th>
        <th class="events-col px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-sm font-semibold text-gray-700 uppercase tracking-wider">Events</th>
        <th class="model-col px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-sm font-semibold text-gray-700 uppercase tracking-wider">Model</th>
        <th class="activity-col px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-sm font-semibold text-gray-700 uppercase tracking-wider">Activity</th>
        <th class="meta-col px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-sm font-semibold text-gray-700 uppercase tracking-wider">Meta</th>
    </tr>
    </thead>

    <tbody class="divide-y">
    @forelse($audits as $audit)
        <tr class="hover:bg-gray-50">
            <td class="px-1 py-1 text-sm text-center">{{ $audit->id }}</td>

            <td class="when-col px-1 py-1 text-sm text-center">
                {{ $audit->created_at->diffForHumans() }}<br>
                <span class="text-xs text-gray-400">{{ $audit->created_at }}</span>
            </td>

            <td class="user-col px-1 py-1 text-sm text-center">
                @if($audit->user)
                    <div class="font-semibold">{{ $audit->user->name }}</div>
                    <div class="text-xs text-gray-400">ID: {{ $audit->user->id }}</div>
                @else
                    <span class="italic text-gray-500">System</span>
                @endif
            </td>

            <td class="events-col px-1 py-1 text-sm text-center">
                <span class="px-2 py-1 rounded text-xs
                    {{ $audit->event == 'created' ? 'bg-green-100 text-green-700' :
                       ($audit->event == 'deleted' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ ucfirst($audit->event) }}
                </span>
            </td>

            <td class="model-col px-1 py-1 text-sm text-center">
                <div class="font-medium">{{ class_basename($audit->auditable_type) }}</div>
                <div class="text-xs text-gray-400">ID: {{ $audit->auditable_id }}</div>
            </td>

            <td class="activity-col px-1 py-1 text-sm text-xs">
                @if($audit->old_values)
                    <strong>Old:</strong>
                    <pre class="bg-gray-50 p-2 rounded">{{ json_encode($audit->old_values, JSON_PRETTY_PRINT) }}</pre>
                @endif

                @if($audit->new_values)
                    <strong>New:</strong>
                    <pre class="bg-gray-50 p-2 rounded">{{ json_encode($audit->new_values, JSON_PRETTY_PRINT) }}</pre>
                @endif
            </td>

            <td class="meta-col px-1 py-1 text-center">
                <button
                    type="button"
                    class="px-1.5 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                    onclick="openModelHistory('{{ addslashes(class_basename($audit->auditable_type)) }}', '{{ $audit->auditable_id }}')">
                    Model History
                </button>
            </td>

        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center py-4 text-gray-500">No audit records found.</td>
        </tr>
    @endforelse
    </tbody>
</table>


<!-- Modal -->
<div id="modelHistoryModal"
     class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center px-4">

    <div class="bg-white rounded-lg  max-w-3xl w-full max-h-[80vh] overflow-y-auto p-6 relative">

        <div class="flex justify-between items-center mb-4">
            <h3 id="modelHistoryTitle" class="text-lg font-semibold">Model History</h3>
            <button id="closeModelHistory" class="px-1.5 py-1.5 bg-white-600 hover:bg-red-700 text-black rounded-lg text-sm border">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
  <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
</svg>

            </button>
        </div>

        <div id="modelHistoryContent" class="space-y-4">
            <div class="text-gray-500">Loading…</div>
        </div>

    </div>
</div>

<script>


    function toggleFilters() {
        const form = document.getElementById("filterSection");
        form.classList.toggle("hidden");
    }


function toggleColumnSelector() {
    document.getElementById("columnSelector").classList.toggle("hidden");
}

/* Column Show/Hide Logic */

// document.addEventListener('DOMContentLoaded', function () {
//   const store = new Map();
//   function forEachColClass(fn) {
//     document.querySelectorAll('[class]').forEach(el => {
//       el.classList.forEach(c => {
//         if (c.endsWith('-col')) fn(el, c);
//       });
//     });
//   }
//   forEachColClass((el, colClass) => {
//     const key = colClass;
//     if (!store.has(el)) store.set(el, el.style.display || '');
//   });
//   function setColumnVisibility(colName, visible) {
//     document.querySelectorAll('.' + colName + '-col').forEach(el => {
//       if (visible) {
//         const prev = store.get(el);
//         el.style.display = prev === undefined ? '' : prev;
//       } else {
//         el.style.display = 'none';
//       }
//     });
//   }
//   document.querySelectorAll('.column-toggle').forEach(chk => {
//     chk.addEventListener('change', function () {
//       setColumnVisibility(this.value, this.checked);
//     });
//     setColumnVisibility(chk.value, chk.checked);
//   });
// });

function openModelHistory(modelType, modelId) {
    const modal = document.getElementById('modelHistoryModal');
    const content = document.getElementById('modelHistoryContent');
    const title = document.getElementById('modelHistoryTitle');

    modal.classList.remove('hidden');
    title.innerText = modelType + " History";
    content.innerHTML = "<div class='text-gray-500'>Loading…</div>";

    fetch(`/audit/history?auditable_type=${modelType}&auditable_id=${modelId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        if (!res.ok) throw new Error("Page not found");
        return res.text();
    })
    .then(html => content.innerHTML = html)
    .catch(() => content.innerHTML = "<div class='text-red-500'>Error loading history.</div>");
}

document.getElementById("closeModelHistory").onclick = () =>
    document.getElementById("modelHistoryModal").classList.add("hidden");

document.getElementById("modelHistoryModal").onclick = (e) => {
    if (e.target.id === "modelHistoryModal") {
        document.getElementById("modelHistoryModal").classList.add("hidden");
    }
};

document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        document.getElementById("modelHistoryModal").classList.add("hidden");
    }
});
</script>
@endsection
