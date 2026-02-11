{{-- @extends('layouts.custom-admin')

@section('title', 'Notifications','bold') --}}
{{-- @php
    $unreadCount = auth()->user()->unreadNotifications->count();
    $recent = auth()->user()->notifications()->latest()->take(10)->get();
@endphp --}}

@if(auth()->check())
<div class="nav-notifications position-relative">

    <a href="#" id="notifBell" class="position-relative">
        <i class="fa fa-bell"></i>

        @if($unreadCount > 0)
            <span id="notifCount" class="badge badge-danger">
                {{ $unreadCount }}
            </span>
        @endif
    </a>

    <div id="notifDropdown"
         class="notif-dropdown"
         style="display:none;">

        <div class="notif-header d-flex justify-content-between align-items-center">
            <strong>Notifications</strong>
            <a href="#" id="markAllRead">Mark all read</a>
        </div>

        <ul class="list-unstyled mb-0">
            @forelse($recentNotifications as $n)
                @php $data = $n->data ?? []; @endphp

                {{-- <li class="notif-item {{ is_null($n->read_at) ? 'unread' : '' }}"
                    data-id="{{ $n->id }}"
                    data-url="{{ $data['url'] ?? '' }}">

                    <div class="notif-link px-2 py-2">
                        <div class="notif-title font-weight-bold">
                            {{ $data['title'] ?? 'Notification' }}
                        </div>
                        <div class="notif-msg small" style="word-wrap: break-word; max-width: 300px;">
                            {{ $data['message'] ?? '' }}
                        </div>
                        <div class="notif-time small text-muted">
                            {{ $n->created_at->diffForHumans() }}
                        </div>
                </div>
                </li> --}}
                   <li class="notif-item {{ is_null($n->read_at) ? 'unread' : '' }}"
                        data-id="{{ $n->id }}"
                         data-url="{{ $data['url'] ?? '' }}">
                             <div class="notif-link">
                                 <div class="notif-content">
                                   <div class="notif-title">
                                    {{ $data['title'] ?? 'Notification' }}
                                    </div>
                                     <div class="notif-msg">
                                          {{ $data['message'] ?? '' }}
                                       <div class="notif-time">
                                        {{ $n->created_at->diffForHumans() }}
                                         </div>
                                     </div>
                                 </div>
                    </li>
            @empty
                <li class="text-muted text-center py-3">
                    No notifications
                </li>
            @endforelse
        </ul>

        <div class="notif-footer text-center">
            <a href="{{ route('masterapp.notifications.index') }}">
                View all
            </a>
        </div>
    </div>
</div>
@endif



@push('styles')
<style>
.notif-dropdown {
    width: 320px;
    background: #fff;
    border: 1px solid #ddd;
    position: absolute;
    right: 0;
    z-index: 1000;
    padding: 10px;
    border-radius: 4px;
}
.notif-item.unread {
    background: #f6f8fb;
}
.badge {
    position: absolute;
    top: -4px;
    right: -4px;
}
</style>
@endpush



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const bell = document.getElementById('notifBell');
    const dropdown = document.getElementById('notifDropdown');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!bell || !dropdown || !csrf) return;

    bell.addEventListener('click', e => {
        e.preventDefault();
        dropdown.style.display =
            dropdown.style.display === 'none' ? 'block' : 'none';
    });

    // mark single notification
    document.querySelectorAll('.notif-item').forEach(item => {
        item.addEventListener('click', () => {
            const id = item.dataset.id;
            const url = item.dataset.url;

            fetch(`{{ route('masterapp.notifications.read', ':id') }}`.replace(':id', id), {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                }
            }).then(() => {
                item.classList.remove('unread');

                const badge = document.getElementById('notifCount');
                if (badge) {
                    const n = parseInt(badge.innerText) - 1;
                    n <= 0 ? badge.remove() : badge.innerText = n;
                }

                if (url) window.location.href = url;
            });
        });
    });

    // mark all read
    document.getElementById('markAllRead')?.addEventListener('click', e => {
        e.preventDefault();

        fetch(`{{ route('masterapp.notifications.read-all') }}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        }).then(() => {
            document.querySelectorAll('.notif-item')
                .forEach(i => i.classList.remove('unread'));
            document.getElementById('notifCount')?.remove();
        });
    });
});
</script>
@endpush
