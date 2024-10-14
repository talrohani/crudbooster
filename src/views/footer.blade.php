<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-{{ cbLang('right') }} hidden-xs">
        {{ cbLang('powered_by') }} {{Session::get('appname')}}
    </div>
    <!-- Default to the left -->
    <strong class="pull-<?php echo e(cbLang('left')); ?>">{{ cbLang('copyright') }} &copy; <?php echo date('Y') ?>. {{ cbLang('all_rights_reserved') }} .</strong>
    &nbsp;
</footer>
