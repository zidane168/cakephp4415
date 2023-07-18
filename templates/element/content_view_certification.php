<!-- languages -->
<?php

use Cake\Routing\Router;

if (isset($languages) && !empty($languages)) : ?>
    <h4>
        <div class="border-content-view-template"> <?php echo __d('staff', 'certification') ?> </div>
    </h4>

    <div class="row" style="margin-top: 30px;">
        <?php foreach ($languages as $language) : ?>
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4 class="box-title center">
                            <?php if (isset($language['alias'])) {
                                switch (h($language['alias'])) {
                                    case 'zh_HK':
                                        echo __('zh_HK');
                                        break;
                                    case 'zh_CN':
                                        echo __('zh_CN');
                                        break;
                                    case 'en_US':
                                        echo __('en_US');
                                        break;
                                    default:
                                        break;
                                }
                            }
                            ?>
                        </h4>
                    </div>

                    <div class="card-body">
                        <?php foreach ($language_input_fields as $language_input_field) : ?>
                            <?php

                            if (isset($language[$language_input_field]) && $language[$language_input_field] != "") {
                                $ico = '';
                                $style = '';

                                if (strpos($language_input_field, 'description') !== false) {
                                    $style = 'font-style:italic; color: #ccc;';
                                }

                                if (strpos($language_input_field, 'address') !== false) {
                                    $style = 'color: #333;';
                                    $ico = '<i class="fa fa-map"></i>';
                                }

                                if (
                                    strpos($language_input_field, 'time') !== false ||
                                    strpos($language_input_field, 'open') !== false
                                ) {
                                    $style = 'color: #333;';
                                    $ico = '<i class="fa fa-clock-o"></i> ';
                                }

                                if (
                                    strpos($language_input_field, 'hotline') !== false ||
                                    strpos($language_input_field, 'phone') !== false
                                ) {
                                    $style = 'color: #333;';
                                    $ico = '<i class="fa fa-phone"></i> ';
                                }

                            ?>
                                <h5 style="padding: 10px 0; <?= $style; ?>">
                                    <?= $ico; ?>
                                    <?= $language[$language_input_field] ?>
                                </h5>
                            <?php
                            }
                            ?>
                        <?php endforeach ?>
                    </div>

                    <div class="card-body">
                        <?php if (isset($_data['link_zho'])) echo  $this->Html->link($_data['link_zho'], $_data['link_zho']); ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>
<!-- /languages -->

<!-- images -->
<?php $id_cover_images = isset($id_collapse_image) ? $id_collapse_image : 'collapseExample'; ?>
<?php if (isset($images) && !empty($images)) : ?>
    <h4>

        <?php if (isset($title)) { ?>
            <i> <?= $title ?> </i>
        <?php } else { ?>

            <div class="border-content-view-template"> Images: </div>
        <?php } ?>

        <!--	<button type="button" class="btn btn-box-tool" data-toggle="collapse" data-target="#<?= $id_cover_images ?>" aria-expanded="true" aria-controls="<?= $id_cover_images ?>">
			<i class="fa fa-eercast"></i>
		</button>-->
    </h4>

    <!-- <div class="col-md-12 collapse show"  id="<?= $id_cover_images ?>"> -->
    <?php foreach ($images as $image) : ?>
        <div class="row border-top mt-15 mb-15">
            <div class="col-md-12">
                <div class="text-left bold"> <?= isset($image['type']) ? $image['type'] : '' ?> </div>
                <!-- <span class="thumbnail fancybox preview"  -->

                <span class="img-thumbnail preview fancybox" href="<?= Router::url('/', true) . $image['path']; ?>" data-fancybox-group="gallery" data-toggled="off">
                    <img class="img-repsonsive" style="width: 30%" src="<?= Router::url('/', true) . $image['path']; ?>" />
                </span>
            </div>
        </div>
    <?php endforeach ?>
    </div>
<?php endif ?>
<!-- /images -->

<script type="text/javascript">
    $(function() {
        $('.fancybox').fancybox({});
    });
</script>