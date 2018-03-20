jQuery(document).ready(function () {
    if ($('.timetable-type', this)[0]) {
        var currentFlagColor = null;
        var currentFlagName = null;
        $('.timetable-type').on('click', '.timetable_flags span', function (e) {
            var clicked = $(e.target);
            var container = clicked.parent().parent();
            container.attr('name', clicked.attr('name'));
            currentFlagColor = clicked.css('background-color');
            currentFlagName = clicked.attr('name');
        });
        $('.timetable-type').on('click', '.timetable_variants span', function (e) {
            var clicked = $(e.target);
            if (clicked.attr('name') === currentFlagName) {
                clicked.attr('name', 'null');
                clicked.css('background-color', '');
            } else {
                clicked.attr('name', currentFlagName);
                clicked.css('background-color', currentFlagColor);
            }
        });
        $('.timetable-type').on('click', '.timetable_add', function (e) {
            e.preventDefault();
            var clicked = $(e.target);
            var container = clicked.parent().parent();
            var variants = $(".timetable_variants:last", container);
            var newVariant = variants.clone();
            newVariant.removeAttr('name');
            newVariant.children().css('background-color', '').attr('name', 'null');
            variants.after(newVariant);
        });
        $('.timetable-type').on('click', '.timetable_remove', function (e) {
            e.preventDefault();
            var clicked = $(e.target);
            var container = clicked.parent().parent();
            var variants = $(".timetable_variants", container);
            if (variants.length > 1) {
                //variants.last().remove();
                clicked.parent().remove();
            }
        });
        /*$('.timetable-type').on('click', '.timetable_save', function(e){
            var clicked = $(e.target);
            var container = clicked.parent().parent();
            var input = $("input", container);
            var variants = $(".timetable_variants", container);
            var variants_array = {};
            variants.each(function(i,variant){
                var variant_array = {};
                $(variant).children().each(function(j,child){
                    variant_array[j] = $(child).attr('name');
                });
                if (variant.hasAttribute('name')) {
                    variants_array[$(variant).attr('name')] = variant_array;
                } else {
                    variants_array['new_'+i] = variant_array;
                }
            });
            input.val(JSON.stringify(variants_array));
        });*/
        $('form').on('submit', function (e) {
            e.preventDefault();
            var input = $('.timetable-type input');
            var variants = $(".timetable-type .timetable_variants");
            var variants_array = {};
            variants.each(function (i, variant) {
                var variant_array = {};
                $(variant).children().each(function (j, child) {
                    variant_array[j] = $(child).attr('name');
                });
                if (variant.hasAttribute('name')) {
                    variants_array[$(variant).attr('name')] = variant_array;
                } else {
                    variants_array['new_' + i] = variant_array;
                }
            });
            input.val(JSON.stringify(variants_array));
            this.submit();
        });
        var timetableInput = $('.timetable-type input');
        timetableInput.css('display','none');
        if (timetableInput.val() !== '') {
            var container = timetableInput.parent().parent();
            var presetData = JSON.parse(timetableInput.val());
            var variants = $(".timetable_variants", container);
            $.each(presetData, function (i, data) {
                var newVariant = variants.clone();
                newVariant.attr('name', i);
                newVariant.children('span').each(function (di, e) {
                    var color = $('.timetable_flags span[name=' + data[di] + ']').css('background-color');
                    $(e).attr('name', data[di]);
                    $(e).css('background-color', color);
                });
                variants.before(newVariant);

            });
            variants[0].remove();
        } else {
            timetableInput.val(' ');
        }
    }
});