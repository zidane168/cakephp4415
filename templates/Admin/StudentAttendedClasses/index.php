<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentAttendedClass[]|\Cake\Collection\CollectionInterface $studentAttendedClasses
 */
?>

<div class="container-fluid card full-border">

    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header" style="display: flex; justify-content: space-between">
                    <h3 class="box-title"> <?php echo __d('cidcclass', 'student_attended_classes'); ?> </h3>
                    <div>
                        <?php echo $this->Html->link('<i class="fa  fa-backward" style="margin-right: 10px" ></i>' . __d('cidcclass', 'class_management'), array('controller' => 'CidcClasses', 'action' => 'index'), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('back'))); ?>
                    </div>
                </div> <!-- box-header -->

                <div class="box-body table-responsive attended-class-header">
                    <h3> <?php echo $cidcClass['name']?> </h3>
                    <table class="student-attended-classes" >
                        <tr> 
                            <td class="w-30"> <?php echo __d('cidcclass', 'date_of_lessons') . ': ' . $cidcClass['date_of_lessons']?> </td>
                            <td class="w-25 "> <span class="bl"> </span> <?php echo __('time') . ': ' . $cidcClass['time']?> </td>
                            <td class="w-25 "> <span class="bl"> </span> <?php echo __('date') . ': ' . $cidcClass['date']?> </td>
                            <td class="w-20 "> <span class="bl"> </span> <?php echo __('status') . ': ' . $cidcClass['status']?> </td>
                        </tr>
                        <tr> 
                            <td>  <?php echo __d('cidcclass', 'number_of_lessons') . ': ' . $cidcClass['number_of_lessons']?> </td>
                            <td> <span class="bl"> </span> <?php echo __d('cidcclass', 'target_audience') . ': ' . $cidcClass['target_audience']?> </td>
                            <td> <span class="bl"> </span> <?php echo __d('cidcclass', 'min_max_students') . ': ' . $cidcClass['min_max_students']?>  </td>
                            <td> <span class="bl"> </span> <?php echo __d('cidcclass', 'number_of_register') . ': ' . $cidcClass['number_of_register']?>  </td>
                        </tr>
                    </table> 
                </div>

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Student Attended Class'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('id', __('id')) ?></th> 
                                <th><?= $this->Paginator->sort('StudentAttendedClasses.kid_id', __('name')) ?></th>

                                <?php foreach ($dates as $date):  ?>
                                    <th> <?= $date  ?>  </th>     
                                <?php endforeach; ?> 
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php 
                                $no = 0;
                                foreach ($students as $key => $student) :  ?>
                                <tr>
                                    <td> <?= ++$no; ?> </td>
                                    <td> <?= $student[0]['name'] ?> </td>

                                    <?php  foreach ($dates as $date) :  ?>
                                            <th class=""> 
                                                <?php 
                                                    $exist = false;
                                                    foreach ($student as $s) {
                                                        if ($s['date'] === $date) {
                                                            if ($s['status'] == 1) {    // attended
                                                                echo $this->Html->image('cidckids/student/attend.png', array(
                                                                    "style" => 'width: 22px',
                                                                    "alt"   => 'attended',
                                                                ));
                                                            } elseif ($s['status'] == 2) {    // absent
                                                                echo $this->Html->image('cidckids/student/absent.png', array(
                                                                    "style" => 'width: 22px',
                                                                    "alt"   => 'attended',
                                                                ));
                                                            } elseif ($s['status'] == 3) {    // on leave
    
                                                                echo $this->Html->image('cidckids/student/on-leave.png', array(
                                                                    "style" => 'width: 22px',
                                                                    "alt"   => 'attended',
                                                                ));
                                                            }
                                                            $exist = true;
                                                            break;
                                                        }
                                                    }

                                                    if (!$exist) {
                                                        echo "NOT ATTENDED DAY";
                                                    }

                                                    // if (in_array($student, $date)) {
                                                    //     if ($student['status'] == 1) {    // attended
                                                    //         echo $this->Html->image('cidckids/student/attend.png', array(
                                                    //             "style" => 'width: 22px',
                                                    //             "alt"   => 'attended',
                                                    //         ));
                                                    //     } elseif ($student['status'] == 2) {    // absent
                                                    //         echo $this->Html->image('cidckids/student/absent.png', array(
                                                    //             "style" => 'width: 22px',
                                                    //             "alt"   => 'attended',
                                                    //         ));
                                                    //     } elseif ($student['status'] == 3) {    // on leave

                                                    //         echo $this->Html->image('cidckids/student/on-leave.png', array(
                                                    //             "style" => 'width: 22px',
                                                    //             "alt"   => 'attended',
                                                    //         ));
                                                    //     }
                                                    // } else {
                                                    //     echo "no room";
                                                    // }
                                                ?>  
                                            </th>   
                                    <?php endforeach;    ?> 

                                    <td> 
                                        <?php
                                        if (isset($permissions['StudentAttendedClasses']['edit']) && ($permissions['StudentAttendedClasses']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $key, $cidc_class_id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php  
                                endforeach; ?>
                        </tbody>
                    </table>
                </div>
 
            </div><!-- box, box-primary -->
        </div><!-- .col-12 -->
    </div><!-- row -->
</div> <!-- container -->