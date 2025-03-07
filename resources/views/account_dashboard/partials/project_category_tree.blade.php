<li>
    <label for="id_{{ $category->id }}" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
        <input type="checkbox" id="id_{{ $category->id }}" name="selected_category" @if($project->category_id==$category->id) checked @endif value="{{ $category->id }}" class="category-checkbox">
        <img src="{{ asset('dashboard/assets/images/file.png') }}" width="25" style="border-radius: 5px; margin-bottom: 5px;">
        <span class="category-name">{{ $category->name }}</span>
    </label>
    @if($category->children->isNotEmpty())
        <ul style="@if(in_array($category->id,$project->category->getAllParentIds())) display: block; @else display: none; @endif">
            @foreach($category->children as $child)
                @include('account_dashboard.partials.project_category_tree', ['category' => $child])
            @endforeach
        </ul>
    @endif
</li>