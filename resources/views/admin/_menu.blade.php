<div class="admin-head">
    <div>
        <span class="label-mono is-orange">Khu vực quản trị</span>
        <h1>{{ $title ?? 'Quản trị' }}</h1>
    </div>
</div>

<nav class="admin-nav">
    <a class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">Tổng quan</a>
    <a class="{{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}" href="{{ route('admin.categories.index') }}">Danh mục</a>
    <a class="{{ request()->routeIs('admin.recipes.index') ? 'is-active' : '' }}" href="{{ route('admin.recipes.index') }}">
        Công thức
        @if($pendingCount > 0) <span class="count">{{ $pendingCount }}</span> @endif
    </a>
    <a class="{{ request()->routeIs('admin.recipes.trash') ? 'is-active' : '' }}" href="{{ route('admin.recipes.trash') }}">Thùng rác</a>
</nav>