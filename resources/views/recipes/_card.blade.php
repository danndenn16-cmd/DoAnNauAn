<article class="recipe-card h-100">
    <a href="{{ route('recipes.show', $r) }}" class="recipe-card__img">
        <img src="{{ $r->image_url }}" alt="{{ $r->title }}" loading="lazy">
    </a>
    <div class="recipe-card__cat label-mono">{{ $r->category->name }}</div>
    <h3 class="recipe-card__title">
        <a href="{{ route('recipes.show', $r) }}">{{ $r->title }}</a>
    </h3>
    <div class="recipe-card__meta">
        <span>⏱ {{ $r->cook_time }} phút</span>
        <span>{{ $r->difficulty_label }}</span>
    </div>
    <div class="recipe-card__author">
        <span class="avatar">{{ mb_substr($r->user->name, 0, 1) }}</span>
        {{ $r->user->name }} · {{ $r->created_at->format('d/m/Y') }}
    </div>
</article>