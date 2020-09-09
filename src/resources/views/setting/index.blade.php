<form action="{{ route('admin.lapi.setting') }}" method="post">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">配置设置</h3>
                </div>
                
                <div class="form-horizontal">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="api_close" class="col-sm-2 asterisk control-label">
                                API状态
                            </label>
                            <div class="col-sm-8">
                                <span class="icheck">
                                    <label>
                                        @if(!$setting['api_close'])
                                            <input class="minimal api_close" type="radio" name="api_close" value="0" checked>
                                        @else
                                            <input class="minimal api_close" type="radio" name="api_close" value="0">
                                        @endif
                                        &nbsp;启用&nbsp;&nbsp;
                                    </label>
                                </span>
                                &nbsp;&nbsp;&nbsp;
                                <span class="icheck">
                                    <label>
                                        @if($setting['api_close'])
                                            <input class="minimal api_close" type="radio" name="api_close" value="1" checked>
                                        @else
                                            <input class="minimal api_close" type="radio" name="api_close" value="1">
                                        @endif
                                        &nbsp;关闭维护&nbsp;&nbsp;
                                    </label>
                                </span>
                                <span class="help-block">
                                    <i class="fa fa-info-circle"></i>&nbsp;设置API的启用状态，默认：启用
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="api_close_tip" class="col-sm-2 asterisk control-label">
                                API维护理由
                            </label>
                            <div class="col-sm-8">
                                <textarea name="api_close_tip" class="form-control" rows="5" placeholder="API维护理由">{{ old('api_close_tip', $setting['api_close_tip']) }}</textarea>
                                <span class="help-block">
                                    <i class="fa fa-info-circle"></i>&nbsp;设置API关闭维护的原因，当API关闭维护时不能为空
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="api_app_pre" class="col-sm-2 asterisk control-label">
                                Appid前缀
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="api_app_pre" placeholder="appid前缀" name="api_app_pre" value="{{old('api_app_pre', $setting['api_app_pre'])}}">
                                <span class="help-block">
                                    <i class="fa fa-info-circle"></i>&nbsp;设置app的appid前缀
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="open_putlog" class="col-sm-2 asterisk control-label">
                                记录日志
                            </label>
                            <div class="col-sm-8">
                                @if($setting['open_putlog'])
                                <span class="icheck">
                                    <label class="">
                                        <input type="radio" name="open_putlog" value="1" class="minimal open_putlog" checked> 启用  
                                    </label>
                                </span>
                                &nbsp;&nbsp;&nbsp;
                                <span class="icheck">
                                    <label class="">
                                        <input type="radio" name="open_putlog" value="0" class="minimal open_putlog" > 禁用  
                                    </label>
                                </span>
                                @else
                                <span class="icheck">
                                    <label class="">
                                        <input type="radio" name="open_putlog" value="1" class="minimal open_putlog" > 启用  
                                    </label>
                                </span>
                                &nbsp;&nbsp;&nbsp;
                                <span class="icheck">
                                    <label class="">
                                        <input type="radio" name="open_putlog" value="0" class="minimal open_putlog" checked> 禁用  
                                    </label>
                                </span>
                                @endif
                                <span class="help-block">
                                    <i class="fa fa-info-circle"></i>&nbsp;是否启用日志记录，默认启用。记录日志关闭后，请求数量限制将会失效
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="box-footer">
                        <button type="submit" class="btn btn-info pull-right">保存</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</form>