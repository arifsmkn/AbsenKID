<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['disabled' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['disabled' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<input <?php if($disabled): echo 'disabled'; endif; ?> <?php echo e($attributes->merge(['class' => 'auth-input w-full px-4 py-2.5 rounded-xl shadow-sm transition-colors focus:outline-none focus:ring-2'])); ?>

       style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15); color:#ffffff; --tw-ring-color:#244C6B"
       onfocus="this.style.borderColor='#40647E'" onblur="this.style.borderColor='rgba(255,255,255,0.15)'">
<?php /**PATH /var/www/AbsenKID/resources/views/components/text-input.blade.php ENDPATH**/ ?>