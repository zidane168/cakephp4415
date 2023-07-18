<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\News[]|\Cake\Collection\CollectionInterface $news
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/news_filter', array(
                'data_search' => $data_search
            ));
            ?>
        </div>
    </div>
</div>

<div class="container-fluid card full-border">

    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('news', 'newses'); ?> </h3>

                    <?php if (isset($permissions['News']['add']) && ($permissions['News']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'News'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('News.id', __('id')) ?></th>
                                <th class="text-center"><?php echo '<a href="#">' . __('thumbnail') . '</a>'; ?> </th>
                                <th><?= $this->Paginator->sort('News.id', __d('center', 'title')) ?></th>
                                <th><?= $this->Paginator->sort('News.date', __d('center', 'publish_date')) ?></th>
                                <th><?= $this->Paginator->sort('enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($news as $news) : ?>
                                <tr>
                                    <td><?= $this->Number->format($news->id) ?></td>
                                    <td class="text-center">
                                        <?php
                                        if (isset($news['news_images']) && !empty($news['news_images'])) {
                                            echo $this->Html->image('../' . $news['news_images'][0]["path"], array(
                                                'class' => ' img-thumbnail preview'
                                            ));
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= $this->Html->link(
                                            h($news['NewsLanguages']['title']),
                                            ['controller' => 'Conditions', 'action' => 'view', $news->id]
                                        )  ?>
                                    </td>
                                    <td><?= h($news->date->format('Y-m-d')) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $news->enabled)) ?></td>

                                    <td><?= h($news->created) ?></td>
                                    <td><?= h($news->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['News']['view']) && ($permissions['News']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $news->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['News']['edit']) && ($permissions['News']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $news->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['News']['delete']) && ($permissions['News']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $news->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $news->id)
                                                )
                                            );
                                        }

                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?= $this->element('Paginator'); ?>
            </div><!-- box, box-primary -->
        </div><!-- .col-12 -->
    </div><!-- row -->
</div> <!-- container -->