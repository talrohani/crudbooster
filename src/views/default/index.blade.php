@extends('crudbooster::admin_template')

@section('content')

    @if($index_statistic)
        <div id='box-statistic' class='row'>
            @foreach($index_statistic as $stat)
                <div class="{{ ($stat['width'])?:'col-sm-3' }}">
                    <div class="small-box bg-{{ $stat['color']?:'red' }}">
                        <div class="inner">
                            <h3>{{ $stat['count'] }}</h3>
                            <p>{{ $stat['label'] }}</p>
                        </div>
                        <div class="icon">
                            <i class="{{ $stat['icon'] }}"></i>
                        </div>
                        @if(!is_null($stat['url']) && !empty($stat['url']))
                            <a href="{{ $stat['url'] }}" class="small-box-footer">{{cbLang('action_show_data')}} <i class="fa fa-arrow-circle-down"></i></a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(!is_null($pre_index_html) && !empty($pre_index_html))
        {!! $pre_index_html !!}
    @endif


    @if(g('return_url'))
        <p><a href='{{g("return_url")}}'><i class='fa fa-chevron-circle-{{ cbLang('left') }}'></i>
                &nbsp; {{cbLang('form_back_to_list',['module'=>urldecode(g('label'))])}}</a></p>
    @endif

    @if($parent_table)
        <div class="box box-default">
            <div class="box-body table-responsive no-padding">
                <table class='table table-bordered'>
                    <tbody>
                    <tr class='active'>
                        <td colspan="2"><strong><i class='fa fa-bars'></i> {{ ucwords(urldecode(g('label'))) }}</strong></td>
                    </tr>
                    @foreach(explode(',',urldecode(g('parent_columns'))) as $c)
                        <tr>
                            <td width="25%"><strong>
                                        <?php
                                        if(urldecode(g('parent_columns_alias')))
                                            $label = explode(',',urldecode(g('parent_columns_alias')))[$loop->index];
                                        else
                                            $label = ucwords(str_replace('_',' ',$c));
                                        $trans_name = trans('admin.'.(Str::replace(" ", "_", $label)));
                                        if(!Str::startsWith($trans_name,"admin.")) $label = $trans_name;
                                        echo $label;
                                        ?>
                                </strong></td>
                            <td> {{ $parent_table->$c }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="box">
        <div class="box-header">
            @if($button_bulk_action && ( ($button_delete && CRUDBooster::isDelete()) || $button_selected) )
                <div class="pull-{{ cbLang('left') }}">
                    <div class="selected-action" style="display:inline-block;position:relative;">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i
                                    class='fa fa-check-square-o'></i> {{cbLang("button_selected_action")}}
                            <span class="fa fa-caret-down"></span></button>
                        <ul class="dropdown-menu">
                            @if($button_delete && CRUDBooster::isDelete())
                                <li><a href="javascript:void(0)" data-name='delete' title='{{cbLang('action_delete_selected')}}'><i
                                                class="fa fa-trash"></i> {{cbLang('action_delete_selected')}}</a></li>
                            @endif

                            @if($button_selected)
                                @foreach($button_selected as $button)
                                    <li><a href="javascript:void(0)" data-name='{{$button["name"]}}' title='{{$button["label"]}}'><i
                                                    class="fa fa-{{$button['icon']}}"></i> {{$button['label']}}</a></li>
                                @endforeach
                            @endif

                        </ul><!--end-dropdown-menu-->
                    </div><!--end-selected-action-->
                </div><!--end-pull-left-->
            @endif
            <div class="box-tools pull-{{ cbLang('right') }}" style="position: relative;margin-top: -5px;margin-right: -10px">
                <?php
                    $grid_view_url = Request::fullUrl();
                    $grid_view_url = preg_replace('~(\?|&)grid_view=[^&]*~','$1',$grid_view_url);
                    if(!Request::get('grid_view')){
                        if(strpos(Request::fullUrl(), '?')>0) $grid_view_url .= "&grid_view=true";
                        else $grid_view_url .= "?grid_view=true";
                    }
                ?>
                <a style="margin-top:-23px" href="{{$grid_view_url}}" id='btn_grid_view' data-url-parameter='{{$build_query}}'
                    class="btn btn-sm btn-default">
                    @if(Request::get('grid_view'))
                        <i class="fa fa-table"></i> {{cbLang('table_view')}}
                    @else
                       <i class="fa fa-th-large"></i> {{cbLang('grid_view')}}
                    @endif
                </a>

                @if($button_filter)
                    <a style="margin-top:-23px" href="javascript:void(0)" id='btn_advanced_filter' data-url-parameter='{{$build_query}}'
                       title='{{cbLang('filter_dialog_title')}}' class="btn btn-sm btn-default {{(Request::get('filter_column'))?'active':''}}">
                        <i class="fa fa-filter"></i> {{cbLang("button_filter")}}
                    </a>
                @endif

                <form method='get' style="display:inline-block;width: 260px;" action='{{Request::url()}}'>
                    <div class="input-group">
                        <input type="text" name="q" value="{{ Request::get('q') }}" class="form-control input-sm pull-{{ cbLang('right') }}"
                               placeholder="{{cbLang('filter_search')}}"/>
                        {!! CRUDBooster::getUrlParameters(['q']) !!}
                        <div class="input-group-btn">
                            @if(Request::get('q'))
                                <?php
                                $parameters = Request::all();
                                unset($parameters['q']);
                                $build_query = urldecode(http_build_query($parameters));
                                $build_query = ($build_query) ? "?".$build_query : "";
                                $build_query = (Request::all()) ? $build_query : "";
                                ?>
                                <button type='button' onclick='location.href="{{ CRUDBooster::mainpath().$build_query}}"'
                                        title="{{cbLang('button_reset')}}" class='btn btn-sm btn-warning'><i class='fa fa-ban'></i></button>
                            @endif
                            <button type='submit' class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>


                <form method='get' id='form-limit-paging' style="display:inline-block" action='{{Request::url()}}'>
                    {!! CRUDBooster::getUrlParameters(['limit']) !!}
                    <div class="input-group">
                        <select onchange="$('#form-limit-paging').submit()" name='limit' style="width: 65px;" class='form-control input-sm'>
                            <option {{($limit==5)?'selected':''}} value='5'>5</option>
                            <option {{($limit==10)?'selected':''}} value='10'>10</option>
                            <option {{($limit==20)?'selected':''}} value='20'>20</option>
                            <option {{($limit==25)?'selected':''}} value='25'>25</option>
                            <option {{($limit==50)?'selected':''}} value='50'>50</option>
                            <option {{($limit==100)?'selected':''}} value='100'>100</option>
                            <option {{($limit==200)?'selected':''}} value='200'>200</option>
                        </select>
                    </div>
                </form>

            </div>

            <br style="clear:both"/>

        </div>
        <div class="box-body table-responsive no-padding">
            @include("crudbooster::default.table")
        </div>
    </div>

    @if(!is_null($post_index_html) && !empty($post_index_html))
        {!! $post_index_html !!}
    @endif

@endsection
