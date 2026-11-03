<div class="admin-head">
    <div>
        <span class="label-mono is-orange">{{ auth()->user()->isAdmin() ? 'Quản trị viên' : 'Biên tập viên' }}</span>
        <h1>{{ $title ?? 'Quản trị' }}</h1>
    </div>
</div>