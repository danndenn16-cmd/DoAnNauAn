@extends('layouts.admin')
@section('title', 'Quản trị')

@section('content')
@include('admin._menu', ['title' => 'Tổng quan'])

@php $maxBar = max($topCategories->max('recipes_count'), 1); @endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="label-mono">Công thức</div>
            <div class="stat-card__n">{{ $stats['recipes'] }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card {{ $stats['pending'] > 0 ? 'stat-card--accent' : '' }}">
            <div class="label-mono">Chờ duyệt</div>
            <div class="stat-card__n">{{ $stats['pending'] }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="label-mono">Danh mục</div>
            <div class="stat-card__n">{{ $stats['categories'] }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="label-mono">Thành viên</div>
            <div class="stat-card__n">{{ $stats['users'] }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <section class="panel">
            <div class="panel__head">
                <h3>Công thức chờ duyệt</h3>
                <a class="small" href="{{ route('admin.recipes.index', ['status' => 'pending']) }}">Xem tất cả</a>
            </div>
            <ul class="panel-list">
                @forelse($pendingRecipes as $r)
                <li>
                    <span>
                        <a href="{{ route('recipes.show', $r) }}">{{ $r->title }}</a>
                        <div class="small text-muted">{{ $r->user->name }} · {{ $r->created_at->format('d/m/Y H:i') }}</div>
                    </span>
                    <form action="{{ route('admin.recipes.approve', $r) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm btn-orange">Duyệt</button>
                    </form>
                </li>
                @empty
                <li class="text-muted">Không có công thức nào đang chờ duyệt. 🎉</li>
                @endforelse
            </ul>
        </section>
    </div>

    <div class="col-lg-5">
        <section class="panel">
            <div class="panel__head">
                <h3>Công thức theo danh mục</h3>
            </div>
            <div class="panel__body">
                @forelse($topCategories as $c)
                <div class="bar-row">
                    <span class="bar-row__label">{{ $c->name }}</span>
                    <span class="bar-row__track">
                        <span class="bar-row__fill" style="display:block;width:{{ ($c->recipes_count / $maxBar) * 100 }}%"></span>
                    </span>
                    <span class="bar-row__value">{{ $c->recipes_count }}</span>
                </div>
                @empty
                <span class="text-muted">Chưa có danh mục.</span>
                @endforelse
            </div>
        </section>
    </div>

    <div class="col-lg-7">
        <section class="panel">
            <div class="panel__head">
                <h3>Được yêu thích nhất</h3>
            </div>
            <ul class="panel-list">
                @forelse($topFavorites as $r)
                <li>
                    <span>
                        <a href="{{ route('recipes.show', $r) }}">{{ $r->title }}</a>
                        <div class="label-mono">{{ $r->category->name }}</div>
                    </span>
                    <span class="badge bg-warning">♥ {{ $r->favorited_by_count }}</span>
                </li>
                @empty
                <li class="text-muted">Chưa có dữ liệu.</li>
                @endforelse
            </ul>
        </section>
    </div>

    <div class="col-lg-5">
        <section class="panel">
            <div class="panel__head">
                <h3>Thành viên mới</h3>
            </div>
            <ul class="panel-list">
                @foreach($latestUsers as $u)
                <li>
                    <span class="d-flex align-items-center gap-2">
                        <span class="avatar">{{ mb_substr($u->name, 0, 1) }}</span>
                        <span>
                            {{ $u->name }}
                            <div class="small text-muted">{{ $u->email }}</div>
                        </span>
                    </span>
                    <span class="label-mono">{{ $u->created_at->format('d/m/Y') }}</span>
                </li>
                @endforeach
            </ul>
        </section>
    </div>
</div>
@endsection