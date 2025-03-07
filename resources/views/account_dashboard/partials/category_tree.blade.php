<li>
    <label for="id_{{ $category->id }}" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
        <input type="checkbox" id="id_{{ $category->id }}" name="selected_category" value="{{ $category->id }}" class="category-checkbox">
        <img src="{{ asset('dashboard/assets/images/file.png') }}" width="25" style="border-radius: 5px; margin-bottom: 5px;">
        <span class="category-name">{{ $category->name }}</span>
    </label>
    @if($category->children->isNotEmpty())
        <ul style="display: none;">
            @foreach($category->children as $child)
                @include('account_dashboard.partials.category_tree', ['category' => $child])
            @endforeach
        </ul>
    @endif
</li>