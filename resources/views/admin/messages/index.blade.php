@extends('layouts.admin')
@section('title', 'Tin nhắn')

@section('content')
@include('admin._menu', ['title' => 'Tin nhắn'])

<div class="row g-3">
    <div class="col-lg-4">
        <div class="dm-box">
            <div class="dm-head"><strong>Cuộc trò chuyện</strong></div>

            @forelse($threads as $t)
                <a href="{{ route('admin.messages.index', ['user' => $t->id]) }}"
                   class="conv {{ $active && $active->id === $t->id ? 'is-active' : '' }}">
                    <span class="avatar">{{ mb_substr($t->name, 0, 1) }}</span>
                    <span class="flex-grow-1" style="min-width:0">
                        <span class="conv__name">
                            <span>{{ $t->name }}</span>
                            @if($t->unread_count > 0 && (!$active || $active->id !== $t->id))
                                <span class="msg-badge">{{ $t->unread_count }}</span>
                            @endif
                        </span>
                        <span class="conv__last d-block">{{ \Illuminate\Support\Str::limit($lastBodies[$t->id] ?? '', 40) }}</span>
                    </span>
                </a>
            @empty
                <div class="p-3 text-muted">Chưa có thành viên nào nhắn tin.</div>
            @endforelse
        </div>
    </div>

    <div class="col-lg-8">
        <div class="dm-box">
            @if($active)
                <div class="dm-head">
                    <span class="avatar">{{ mb_substr($active->name, 0, 1) }}</span>
                    <div>
                        <strong>{{ $active->name }}</strong>
                        <div class="label-mono">{{ $active->email }}</div>
                    </div>
                </div>

                @include('messages._thread', [
                    'viewer'     => auth()->user(),
                    'pollUrl'    => route('admin.messages.poll', $active),
                    'storeUrl'   => route('admin.messages.store', $active),
                    'otherLabel' => $active->name,
                    'emptyText'  => 'Chưa có tin nhắn. Hãy gửi lời chào đầu tiên.',
                ])
            @else
                <div class="dm-empty p-5">Chọn một cuộc trò chuyện ở bên trái.</div>
            @endif
        </div>
    </div>
</div>
@endsection