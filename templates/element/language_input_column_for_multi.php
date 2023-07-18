<?php

use Cake\Utility\Hash;

$arr_title = array(
    'name'             => '<font color="red">*</font>' . __('name'),
    'body'             =>  __d('setting', 'body'),
    'nick_name'             =>  __d('parent', 'nick_name'),
    'description'     => __('description'),
    'address'         => '<font color="red">*</font>' . __('address'),
    'remark'         => __('remark'),
    'agency_company_name'         => __d('member', 'agency_company_name'),
    'title'                     => '<font color="red"> * </font>' . __('title'),
    'content'                     => '<font color="red"> * </font>' . __('content'),

);

$arr_language_tabs = array();
foreach ($available_language as $lang) {
    $arr_language_tabs[$lang] = __($lang . '_name');
}
?>

<?php if ((isset($languages_list) && !empty($languages_list))) : ?>

    <?php
    if (isset($languages_edit_data) && !empty($languages_edit_data) && $languages_edit_data !== 'false') {
        $languages_edit_data = Hash::combine($languages_edit_data, '{n}.alias', '{n}');
    }
    ?>
    <div class="row" style="<?= (isset($hidden) && $hidden) ? 'display: none;' : ' ' ?>;  margin-bottom: 10px">

        <?php
        foreach ($languages_list as $key => $language) :
            if (isset($languages_edit_data[$language['alias']])) {

                $col = 12 / 2;
                if (count($languages_list) <= 3) {
                    $col = 12 / count($languages_list);
                }

                echo '<div class="col-md-' . $col . '">';
        ?>
                <div class="col-md-12 p-0" style="text-align: left;">
                    <legend class="color-background-template"><?= isset($arr_language_tabs[$language['alias']]) ? $arr_language_tabs[$language['alias']] : '' ?></legend>
                </div>
                <?php
                foreach ($language_input_fields as $field) {
                    $attr = array(
                        'class' => 'form-control',
                        'style' => 'margin-bottom:20px',
                        'value' => isset($languages_edit_data[$language['alias']][$field]) ? $languages_edit_data[$language['alias']][$field] : NULL,
                    );

                    if (strpos($field, 'id') !== false) {
                        $attr['type'] = 'hidden';
                    }

                    if (strpos($field, 'alias') !== false) {
                        $attr['type'] = 'hidden';
                    }

                    if (
                        (strpos($field, 'description') !== false) ||
                        (strpos($field, 'remark') !== false) ||
                        (strpos($field, 'content') !== false)
                    ) {
                        $attr['class'] = 'form-control ckeditor';
                        $attr['type'] = 'textarea';
                    }

                    if (isset($arr_title[$field])) {
                        if (
                            $field == "name" || $field == "title" || $field == "description" || $field == "author" || $field ==
                            "content"
                        ) {
                            $attr['required'] = 'true';
                        }

                        $attr['label'] = $arr_title[$field];
                    }
                    $attr['escape'] = false;
                    echo '<div>' . $this->Form->control($languages_model  . '.' . $index_items . '.' . $key . '.' . $field, $attr) . '</div>';
                }

                echo '</div>';
            } else {
                $col = 12 / 2;
                if (count($languages_list) <= 3) {
                    $col = 12 / count($languages_list);
                }
                echo '<div class="col-md-' . $col . '">';
                ?>
                <div class="col-md-12 p-0" style="text-align: left;">
                    <legend class="color-background-template"><?= isset($arr_language_tabs[$language['alias']]) ? $arr_language_tabs[$language['alias']] : '' ?></legend>
                </div>
        <?php
                foreach ($language_input_fields as $field) {
                    $attr = array(
                        'class' => 'form-control',
                        'style' => 'margin-bottom:20px',
                    );

                    if (strpos($field, 'id') !== false) {
                        $attr['type'] = 'hidden';
                    }

                    if (strpos($field, 'alias') !== false) {
                        $attr['type'] = 'hidden';
                        $attr['value'] = $language['alias'];
                    }

                    if (
                        (strpos($field, 'scope_of_services') !== false) ||
                        (strpos($field, 'description') !== false) ||
                        (strpos($field, 'content') !== false)
                    ) {
                        $attr['class'] = 'form-control ckeditor';
                        $attr['type'] = 'textarea';
                    }

                    if (isset($arr_title[$field])) {
                        if (
                            $field == "title" ||
                            $field == "description" ||
                            $field == "name" ||
                            $field == "author" ||
                            $field == "content" ||
                            $field == "address"
                        ) {
                            $attr['required'] = 'true';
                        }

                        $attr['label'] = $arr_title[$field];
                    }
                    $attr['escape'] = false;

                    echo '<div>' . $this->Form->control($languages_model  . '.' . $index_items . '.' . $key . '.' . $field, $attr) . '</div>';
                }
                echo '</div>';
            }
        endforeach ?>

    </div>
<?php endif; ?>