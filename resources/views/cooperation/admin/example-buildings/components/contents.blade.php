<h4>@lang('cooperation/admin/example-buildings.components.contents.title')</h4>
<ul class="nav nav-tabs" role="tablist">
    {{-- tabs --}}
    @if(isset($exampleBuilding) && $exampleBuilding instanceof \App\Models\ExampleBuilding)
        @foreach($exampleBuilding->contents as $content)
            <li role="presentation">
                <a href="#{{ $content->id }}" aria-controls="{{ $content->id }}" role="tab" data-toggle="tab"
                   @if(count($errors->get("content.{$content->id}.*")) > 0) style="border: 1px solid #a94442" @endif>
                    {{ $content->build_year }}
                </a>
            </li>
        @endforeach
    @endif
    <li class="@if(Route::currentRouteName() === "cooperation.admin.example-buildings.create") active @endif">
        <a href="#new" aria-controls="new" role="tab" data-toggle="tab"
           @if(count($errors->get("content.new.*")) > 0) style="border: 1px solid #a94442" @endif>
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </li>
</ul>
<div class="tab-content">
    {{-- tab contents --}}
    @if(isset($exampleBuilding) && $exampleBuilding instanceof \App\Models\ExampleBuilding)
        @foreach($exampleBuilding->contents as $content)
            <div role="tabpanel" class="tab-pane" id="{{ $content->id }}">
                @include('cooperation.admin.example-buildings.components.content-table', ['content' => $content])
            </div>
        @endforeach
    @endif
    <div role="tabpanel" class="tab-pane @if(Route::currentRouteName() === "cooperation.admin.example-buildings.create") active @endif" id="new">
        @include('cooperation.admin.example-buildings.components.content-table', ['content' => null])
    </div>
</div>