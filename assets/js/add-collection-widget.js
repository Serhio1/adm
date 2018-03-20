jQuery(document).ready(function () {
    var addCollectionWidget = '<div class="add-collection-widget">+</div>';
    var removeCollectionWidget = '<div class="remove-collection-widget"> &mdash; </div>';
    jQuery('.allow-add').parent().append(addCollectionWidget);
    jQuery('.allow-remove').parent().append(removeCollectionWidget);
    $(document.body).on('click', '.add-collection-widget', function(e){
        e.preventDefault();
        var list = $(this).prevAll('.allow-add');
        var counter = list.children().length;
        var newWidget = list.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, counter);
        if (list.hasClass('allow-remove')) {
            //newWidget = newWidget.replace(/<\/div>/g, removeCollectionWidget + '<\/div>');
            //newWidget = newWidget + removeCollectionWidget;
        }
        //counter++;
        list.append(newWidget);
    });

    $(document.body).on('click', '.remove-collection-widget', function(e){
        e.preventDefault();
        var list = $(this).prevAll('.allow-remove');
        //var list = field.parent();
        list.children().last().remove();
        //var counter = list.children().length;
    });
    /*jQuery('.remove-collection-widget').click(function (e) {

    });*/
});