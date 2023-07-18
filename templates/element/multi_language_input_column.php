<div class="form-group languages-upload ">

    <?php
    if (!empty($languages_edit_data)) {
        foreach ($languages_edit_data as $key => $item) : ?>
            <div class="welllanguage well-sm">
                <div class="row images-upload-row">
                    <div class="col-md-11">
                        <?php
                        echo $this->element('language_input_column_for_multi', array(
                            'languages_model'       => $languages_model,
                            'languages_list'        => $languages_list,
                            'language_input_fields' => $language_input_fields,
                            'languages_edit_data'   => $item->professional_certification_languages,
                            'index_items'            => $key
                        ));
                        ?>
                    </div>


                    <div class="text-right col-md-1 images-buttons">
                        <?php
                        echo $this->Html->link('<i class="far fa-times-circle"></i>', '#', array(
                            'class' => 'btn-remove-language',
                            'escape' => false
                        ));
                        ?>
                    </div>

                    <div class="form-group-label col-md-12">
                        <span class="image-type-limitation"></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php } ?>

    <?php
    if (strpos($this->request->getParam('action'), 'edit') === false) : ?>
        <!-- add function -->
        <div class="welllanguage well-sm">
            <div class="row images-upload-row">

                <div class="col-md-11">
                    <?php
                    echo $this->element('language_input_column_for_multi', array(
                        'languages_model'       => $languages_model,
                        'languages_list'        => $languages_list,
                        'language_input_fields' => $language_input_fields,
                        'languages_edit_data'   => false,
                        'index_items'            => 0
                    ));
                    ?>
                </div>

                <div class="text-right col-md-1 images-buttons">
                    <?php
                    echo $this->Html->link('<i class="far fa-times-circle"></i>', '#', array(
                        'class' => 'btn-remove-language',
                        'escape' => false
                    ));
                    ?>
                </div>

                <div class="form-group-label col-md-12">
                    <span class="image-type-limitation"></span>
                </div>
            </div>
        </div>
    <?php endif ?>


    <?php

    if (!isset($can_add) || (isset($can_add) && $can_add)) {   ?>
        <div class="row languages-upload-row-button">
            <div class="text-center col-md-12">
                <?php
                print $this->Html->link('<i class="fas fa-plus"></i> ' . __('add'), '#', array(
                    'class' => 'btn btn-primary btn-new-language btn-new-btn',
                    'escape' => false
                ));
                ?>
            </div>
        </div>
    <?php
    }
    ?>

</div><!-- .form-group -->

<script>

</script>
<script type="text/javascript" charset="utf-8">
    var article_items_language = {
        count: 0
    };
    max_image_language = '<?= isset($total_item) && !empty($total_item) && $total_item > 0 ?  $total_item : 0 ?>';
    var count_edit_data = <?php if (!empty($languages_edit_data)) {
                                echo count($languages_edit_data);
                            } else {
                                echo 0;
                            } ?>;
    var index_language = count_edit_data || 1;
    console.log(index_language);

    $(document).ready(function() {

        article_items_language.count = $('.languages-upload > .welllanguage').length;

        if (max_image_language > 0 && article_items_language.count >= max_image_language) {
            $('.btn-new-language').hide();
        }
 
        $('.btn-remove-language').on('click', function(e) {
            e.preventDefault();

            article_items_language.count--; 
            $('.btn-new-language').show();
            $(this).closest(".welllanguage").remove();
        });

        $('.btn-remove-uploaded-language').on('click', function(e) {
            e.preventDefault();

            var image_id = $(this).data('image-id');

            var remove_hidden_input = '<input type="hidden" name="data[remove_image][]" value="' + image_id + '">';

            article_items_language.count--; 
            $('.btn-new-language').show();

            $(this).parents('.languages-upload').append(remove_hidden_input);
            $(this).closest(".welllanguage").remove();
        });

        $('.btn-new-language').on('click', function(e) {
            e.preventDefault();

            var url = '<?php echo $add_language_input_url; ?>';
            var language_edit_data = <?php echo json_encode($languages_edit_data) ?>;
            if (language_edit_data === 'false') {
                language_edit_data = false
            }
            COMMON.call_ajax({
                type: "GET",
                url: url,
                dataType: 'html',
                cache: false,
                // headers: {
                // 	'X-CSRF-Token' : $('[name="_csrfToken"]').val()
                // },

                data: {
                    count: article_items_language.count,
                    languages_model: <?php echo json_encode($languages_model) ?>,
                    languages_list: <?php echo json_encode($languages_list) ?>,
                    language_input_fields: <?php echo json_encode($language_input_fields) ?>,
                    languages_edit_data: language_edit_data,
                    // index_items: $("input[name='numberElementCertification']").val()
                    index_items: index_language

                },
                success: function(result) {

                    var counter_language = (article_items_language.count - 1);
                    if (counter_language < 0) {
                        $('.languages-upload > .languages-upload-row-button').before(result);

                    } else {
                        $('.languages-upload > .welllanguage').eq(counter_language).after(result);
                    }

                    // article_items_language.count++;
                    article_items_language.count = $('.languages-upload > .welllanguage').length; // count again
                    if (max_image_language > 0 && article_items_language.count >= max_image_language) {
                        $('.btn-new-language').hide();
                    }

                    $('.btn-remove-language').on('click', function(e) {
                        e.preventDefault();

                        article_items_language.count--;
                        $('.btn-new-language').show();
                        $(this).closest(".welllanguage").remove();
                    });

                    // $("input[name='numberElementCertification']").val($("input[name='numberElementCertification']") + 1)
                    index_language++
                },
                error: function(result) {
                    console.log(result);
                }
            });
        });
    });
</script>