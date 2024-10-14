<?php
$ext = pathinfo($value, PATHINFO_EXTENSION);
$images_type = array('jpg', 'png', 'gif', 'jpeg', 'bmp', 'tiff');
if(Storage::exists($value) || file_exists($value)):
if(in_array(strtolower($ext), $images_type)):?>
<a data-lightbox='roadtrip' href='{{asset($value)}}'><img style='max-width:150px' title="Image For {{$form['label']}}" src='{{asset($value)}}'/></a>
<?php else:?>
<a href='{{asset($value)}}' target="_blank"><i class="fa fa-eye"></i> {{cbLang("button_view_file")}}</a> |
<a href='{{asset($value)}}?download=1' target="_blank"><i class="fa fa-download"></i> {{cbLang("button_download_file")}}</a>
<?php endif;?>
<?php endif;?>
