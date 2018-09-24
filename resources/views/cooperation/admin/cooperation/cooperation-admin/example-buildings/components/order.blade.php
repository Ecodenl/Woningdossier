<div class="form-group">
    <label for="order">Order:</label>
    <input type="number" id="order" class="form-control" min="0" name="order" value="@if(isset($exampleBuilding)){{ $exampleBuilding->order }}@endif">
</div>