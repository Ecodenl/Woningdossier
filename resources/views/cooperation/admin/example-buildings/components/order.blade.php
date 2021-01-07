<div class="form-group">
    <label for="order">@lang('cooperation/admin/example-buildings.components.order')</label>
    <input type="number" id="order" class="form-control" min="0" name="order" value="{{ old('order', optional($exampleBuilding ?? null)->order) }}">
</div>