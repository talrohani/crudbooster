@if($form['datatable'])

    @if($form['relationship_table'])
        @push('bottom')
            <script type="text/javascript">
                $(function () {
                    $('#{{$name}}').select2();
                })
            </script>
        @endpush
    @else
        @if($form['datatable_ajax'] == true)

            <?php
            $datatable = @$form['datatable'];
            $where = @$form['datatable_where'];
            $format = @$form['datatable_format'];

            $raw = explode(',', $datatable);
            $url = CRUDBooster::mainpath("find-data");

            $table1 = $raw[0];
            $column1 = $raw[1];

            @$table2 = $raw[2];
            @$column2 = $raw[3];

            @$table3 = $raw[4];
            @$column3 = $raw[5];
            ?>

            @push('bottom')
                <script>
                    $(function () {
                        $('#{{$name}}').select2({
                            placeholder: {
                                id: '-1',
                                text: '{{cbLang('text_prefix_option')}} {{$form['label']}}'
                            },
                            allowClear: true,
                            ajax: {
                                url: '{!! $url !!}',
                                delay: 250,
                                data: function (params) {
                                    var query = {
                                        q: params.term,
                                        format: "{{$format}}",
                                        table1: "{{$table1}}",
                                        column1: "{{$column1}}",
                                        table2: "{{$table2}}",
                                        column2: "{{$column2}}",
                                        table3: "{{$table3}}",
                                        column3: "{{$column3}}",
                                        where: "{!! addslashes($where) !!}"
                                    }
                                    return query;
                                },
                                processResults: function (data) {
                                    return {
                                        results: data.items
                                    };
                                }
                            },
                            escapeMarkup: function (markup) {
                                return markup;
                            },
                            minimumInputLength: 1,
                            @if($value)
                            initSelection: function (element, callback) {
                                var id = $(element).val() ? $(element).val() : "{{$value}}";
                                if (id !== '') {
                                    $.ajax('{{$url}}', {
                                        data: {
                                            id: id,
                                            format: "{{$format}}",
                                            table1: "{{$table1}}",
                                            column1: "{{$column1}}",
                                            table2: "{{$table2}}",
                                            column2: "{{$column2}}",
                                            table3: "{{$table3}}",
                                            column3: "{{$column3}}"
                                        },
                                        dataType: "json"
                                    }).done(function (data) {
                                        callback(data.items[0]);
                                        $('#<?php echo $name?>').html("<option value='" + data.items[0].id + "' selected >" + data.items[0].text + "</option>");
                                    });
                                }
                            }

                            @endif
                        });

                    })
                </script>
            @endpush

        @else
            @push('bottom')
                <script type="text/javascript">
                    $(function () {
                        $('#{{$name}}').select2();
                    })
                </script>
            @endpush
        @endif
    @endif
@else

    @push('bottom')
        <script type="text/javascript">
            $(function () {
                $('#{{$name}}').select2();
            })
        </script>
    @endpush

@endif

<?php $default = ! empty($form['default']) ? $form['default'] : cbLang('text_prefix_option')." ".$form['label'];?>
@if($form['parent_select'])
    <?php
    $parent_select = (count(explode(",", $form['parent_select'])) > 1) ? explode(",", $form['parent_select']) : $form['parent_select'];
    $parent = is_array($parent_select) ? $parent_select[0] : $parent_select;
    $add_field = is_array($parent_select) ? $parent_select[1] : '';
    ?>
    @push('bottom')
        <script type="text/javascript">
            $(function () {
                $('#{{$parent}}, input:radio[name={{$parent}}]').change(function () {
                    var $current = $("#{{$form['name']}}");
                    var parent_id = $(this).val();
                    var fk_name = "{{$parent}}";
                    var fk_value = $(this).val();
                    var datatable = "{{$form['datatable']}}".split(',');
                            @if(!empty($add_field))
                    var add_field = ($("#{{$add_field}}").val()) ? $("#{{$add_field}}").val() : "";
                            @endif
                    var datatableWhere = "{{$form['datatable_where']}}";
                    @if(!empty($add_field))
                    if (datatableWhere) {
                        if (add_field) {
                            datatableWhere = datatableWhere + " and {{$add_field}} = " + add_field;
                        }
                    } else {
                        if (add_field) {
                            datatableWhere = "{{$add_field}} = " + add_field;
                        }
                    }
                            @endif
                    var table = datatable[0].trim('');
                    var label = datatable[1].trim('');
                    var value = "{{$value}}";

                    if (fk_value != '') {
                        $current.html("<option value=''>{{cbLang('text_loading')}} {{$form['label']}}");
                        $.get("{{CRUDBooster::mainpath('data-table')}}?table=" + table + "&label=" + label + "&fk_name=" + fk_name + "&fk_value=" + fk_value + "&datatable_where=" + encodeURI(datatableWhere), function (response) {
                            if (response) {
                                $current.html("<option value=''>{{$default}}");
                                $.each(response, function (i, obj) {
                                    var selected = (value && value == obj.select_value) ? "selected" : "";
                                    $("<option " + selected + " value='" + obj.select_value + "'>" + obj.select_label + "</option>").appendTo("#{{$form['name']}}");
                                })
                                $current.trigger('change');
                            }
                        });
                    } else {
                        $current.html("<option value=''>{{$default}}");
                    }
                })

                $('#{{$parent}}').trigger('change');
                $("input[name='{{$parent}}']:checked").trigger("change");
                $("#{{$form['name']}}").trigger('change');
            })
        </script>
    @endpush

@endif
<div class='form-group no-margin no-padding {{$col_width?:'col-sm-12'}} {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
    <label class='control-label col-sm-12' style="text-align: start">{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! cbLang('this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="col-sm-12">
        <select class='form-control' id="{{$name}}"
                {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} name="{{$name}}{{($form['relationship_table'])?'[]':''}}" {{ ($form['relationship_table'])?'multiple="multiple"':'' }} >

            <?php if (! $form['parent_select']) { ?>

            @if($form['dataenum'])
                <option value=''>{{cbLang('text_prefix_option')}} {{$form['label']}}</option>
                @if($form['default_option'])
                    <option value="0" {{($value == 0) ? "selected" : ""}}>{{$form['default_option']}}</option>
                @endif
                <?php
                $dataenum = $form['dataenum'];
                $dataenum = (is_array($dataenum)) ? $dataenum : explode(";", $dataenum);
                ?>
                @foreach($dataenum as $enum)
                    <?php
                    $val = $lab = '';
                    if (strpos($enum, '|') !== FALSE) {
                        $draw = explode("|", $enum);
                        $val = $draw[0];
                        $lab = $draw[1];
                    } else {
                        $val = $lab = $enum;
                    }

                    $select = ($value == $val) ? "selected" : "";
                    ?>
                    <option {{$select}} value='{{$val}}'>{{$lab}}</option>
                @endforeach
            @endif

            @if($form['datatable'])
                @if($form['relationship_table'])
                    <?php
                    $select_table = explode(',', $form['datatable'])[0];
                    $select_title = explode(',', $form['datatable'])[1];
                    $select_where = $form['datatable_where'];
                    $pk = CRUDBooster::findPrimaryKey($select_table);

                    $result = DB::table($select_table)->select($pk, $select_title);
                    if ($select_where) {
                        $result->whereraw($select_where);
                    }
                    $result = $result->orderby($select_title, 'asc')->get();

                    if($form['datatable_orig'] != ''){
                        $params = explode("|", $form['datatable_orig']);
                        if(!isset($params[2])) $params[2] = "id";
                        $value = DB::table($params[0])->where($params[2], $id)->first()->{$params[1]};
                        $value = explode(",", $value);
                    } else {
                        $foreignKey = CRUDBooster::getForeignKey($table, $form['relationship_table']);
                        $foreignKey2 = CRUDBooster::getForeignKey($select_table, $form['relationship_table']);
                        $value = DB::table($form['relationship_table'])->where($foreignKey, $id);
                        $value = $value->pluck($foreignKey2)->toArray();
                    }

                    foreach ($result as $r) {
                        $option_label = $r->{$select_title};
                        $option_value = $r->id;
                        $selected = (is_array($value) && in_array($r->$pk, $value)) ? "selected" : "";
                        echo "<option $selected value='$option_value'>$option_label</option>";
                    }
                    ?>
                @else
                    @if($form['datatable_ajax'] == false)
                        <option value=''>{{cbLang('text_prefix_option')}} {{$form['label']}}</option>
                        @if($form['default_option'])
                            <option value="0" {{($value == 0) ? "selected" : ""}}>{{$form['default_option']}}</option>
                        @endif
                        <?php
                        $select_table = explode(',', $form['datatable'])[0];
                        $select_title = explode(',', $form['datatable'])[1];
                        $select_where = $form['datatable_where'];
                        $datatable_format = $form['datatable_format'];
                        $select_table_pk = CRUDBooster::findPrimaryKey($select_table);
                        if(empty($select_table_pk)) $select_table_pk = "id";
                        $result = DB::table($select_table)->select($select_table_pk, $select_title);
                        if ($datatable_format) {
                            $result->addSelect(DB::raw("CONCAT(".$datatable_format.") as $select_title"));
                        }
                        $datatable_group = $form['datatable_group'];
                        if($datatable_group){
                            $result->addSelect($datatable_group);
                        }
                        if ($select_where) {
                            $result->whereraw($select_where);
                        }
                        if (CRUDBooster::isColumnExists($select_table, 'deleted_at')) {
                            $result->whereNull('deleted_at');
                        }
                            //$result = $result->get();
                        if($datatable_group)
                            $result = $result->orderby($datatable_group, 'asc')->orderby($select_title, 'asc')->get();
                        else
                            $result = $result->orderby($select_title, 'asc')->get();

                        $group_value = "";
                        $i = 0;
                        foreach ($result as $r) {
                            $i += 1;
                            $is_group = false;
                            if($datatable_group){
                                if($group_value != $r->{$datatable_group}){
                                    $group_value = $r->{$datatable_group};
                                    $is_group = true;
                                    if($i > 0) echo "</optgroup>";
                                    echo "<optgroup label='$group_value'>";
                                }
                            }

                            $option_label = $r->{$select_title};
                            $option_value = $r->$select_table_pk;
                            $selected = ($option_value == $value) ? "selected" : "";
                            echo "<option $selected value='$option_value'>$option_label</option>";
                        }
                        ?>
                    <!--end-datatable-ajax-->
                    @endif

                <!--end-relationship-table-->
                @endif

            <!--end-datatable-->
            @endif
            <?php }  ?> //end if not parent select
        </select>
        <div class="text-danger">
            {!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}
        </div><!--end-text-danger-->
        <p class='help-block'>{{ @$form['help'] }}</p>

    </div>
</div>
