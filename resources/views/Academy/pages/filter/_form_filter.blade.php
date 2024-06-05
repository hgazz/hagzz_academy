<div class="row">
    <div class="col-md-5">
        <label>{{ trans('admin.start_date') }}</label>
        <input type="date" name="start_date" class="form-control">
    </div>
    <div class="col-md-5">
        <label>{{ trans('admin.end_date') }}</label>
        <input type="date" name="end_date" class="form-control">
    </div>
    <div class="col-md-2">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary mt-4">{{ trans('admin.apply') }}</button>
    </div>
</div>
