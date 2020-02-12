<div class="row padding-20">
	<div class="col-md-6">
		<div class="form-group">
			<label class="text-muted">狀態</label>
			<select id="display" name="display" class="form-control selectpicker" title="狀態" data-style="btn-primary">
				<option value="0" {{ isset($config['parameter']['display']) && !$config['parameter']['display']?'selected':'' }}>全部</option>
				<option value="2" {{ isset($config['parameter']['display']) && $config['parameter']['display']==2?'selected':'' }}>已啟用</option>
				<option value="1" {{ isset($config['parameter']['display']) && $config['parameter']['display']==1?'selected':'' }}>已停用</option>
			</select>
			<div class="invalid-feedback"></div>
			<div class="help-feedback"></div>
		</div>
	</div>
	<div class="col-md-6"></div>
	<div class="col-md-12">
		<div class="float-right">
			<div class="btn-group" role="group">
				<a class="btn btn-cons btn-link btn-filter-cancel" href="javascript:;"><i class="fas fa-times"></i> 取消</a>
				<a class="btn btn-cons btn-primary btn-filter-submit" href="javascript:;"><i class="fas fa-check"></i> 送出</a>
			</div>
		</div>
	</div>
</div>