jQuery(document).ready(function($) {

    // Set dropdown as 'multiple'.
    // Set selected cats.
    $('#featured_images_in_rss_cat_exclude').attr('multiple', 'multiple');

    // Init Select2.
    $('#featured_images_in_rss_cat_exclude').select2({
        placeholder: firss_l18n.categories_placheolder,
    });

    $('#featured_images_in_rss_cat_exclude').val(firss_l18n.excluded_cats).trigger('change');

    // Dynamically show custom sizes input.
    $('select[name=featured_images_in_rss_size]').change(function() {

        if ('custom' === $(this).val()) {
            $('.custom-sizes').fadeIn();
        } else {
            $('.custom-sizes').fadeOut();
        }

    });

    $('select[name=featured_images_in_rss_size]').change();
});