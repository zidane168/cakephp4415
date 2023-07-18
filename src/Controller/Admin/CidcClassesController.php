<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use App\MyHelper\MyHelper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Event\EventInterface;

/**
 * CidcClasses Controller
 *
 * @method \App\Model\Entity\CidcClass[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CidcClassesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $PENDING        = $this->CidcClasses->PENDING;
        $PUBLISHED      = $this->CidcClasses->PUBLISHED;
        $UNPUBLISHED    = $this->CidcClasses->UNPUBLISHED;
        $COMPLETED      = $this->CidcClasses->COMPLETED;

        $this->set(compact('PENDING', 'PUBLISHED', 'UNPUBLISHED', 'COMPLETED'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // $obj_Holidays = TableRegistry::get('CidcHolidays');
        // $holidays = $obj_Holidays->get_all_list();

        // $dates = $this->CidcClasses->search_dates_with_conditions(date('Y-m-d', strtotime('2023/04/01')), [2,3,4,5], 11,  $holidays);  
        // $dates = $this->CidcClasses->get_date_of_week_in_range('2023/03/30', '2023/05/01', [2,4,6], 30, $holidays); 
        // pr ($dates); 
        // exit;

        $data_search = $this->request->getQuery();
        $_conditions =  array();

        // get list centers managements belong to administrator
        $session = $this->request->getSession();
        $list_centers_management = $session->read('administrator.list_centers_management');
        if ($list_centers_management && count($list_centers_management) > 0) {
            $_conditions['CidcClasses.center_id IN '] = $list_centers_management;
        }

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['CidcClasses.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['center_id']) && $data_search['center_id'] != "") {
            $_conditions['CidcClasses.center_id'] = intval($data_search['center_id']);
        }

        if (isset($data_search['program_id']) && $data_search['program_id'] != "") {
            $_conditions['CidcClasses.program_id'] = intval($data_search['program_id']);
        }

        if (isset($data_search['course_id']) && $data_search['course_id'] != "") {
            $_conditions['CidcClasses.course_id'] = intval($data_search['course_id']);
        }

        if (isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(CidcClasses.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'CidcClasses.id',
                'CidcClasses.program_id',
                'CidcClasses.center_id',
                'CidcClasses.number_of_register',
                'CidcClasses.course_id',
                'CidcClasses.class_type_id',
                'CidcClasses.status',
                'CidcClasses.fee',
                'CidcClasses.name',
                'CidcClasses.code',
                'CidcClasses.target_audience_from',
                'CidcClasses.target_audience_to',
                'CidcClasses.target_unit',
                'CidcClasses.enabled',
                'CidcClasses.created',
                'CidcClasses.modified',
            ],
            'conditions' => $_conditions,
            'contain' => [
                'Programs' => [
                    'fields' => [
                        'Programs.id'
                    ],
                    'conditions' => [
                        'Programs.enabled' => true
                    ],
                    'ProgramLanguages' => [
                        'fields' => [
                            'ProgramLanguages.program_id',
                            'ProgramLanguages.name'
                        ],
                        'conditions' => [
                            'ProgramLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'Courses' => [
                    'fields' => [
                        'Courses.id'
                    ],
                    'conditions' => [
                        'Courses.enabled' => true
                    ],
                    'CourseLanguages' => [
                        'fields' => [
                            'CourseLanguages.course_id',
                            'CourseLanguages.name'
                        ],
                        'conditions' => [
                            'CourseLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'Centers' => [
                    'fields' => [
                        'Centers.id'
                    ],
                    'conditions' => [
                        'Centers.enabled' => true
                    ],
                    'CenterLanguages' => [
                        'fields' => [
                            'CenterLanguages.center_id',
                            'CenterLanguages.name'
                        ],
                        'conditions' => [
                            'CenterLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'ClassTypes' => [
                    'fields' => [
                        'ClassTypes.id'
                    ],
                    'conditions' => [
                        'ClassTypes.enabled' => true
                    ],
                    'ClassTypeLanguages' => [
                        'fields' => [
                            'ClassTypeLanguages.class_type_id',
                            'ClassTypeLanguages.name'
                        ],
                        'conditions' => [
                            'ClassTypeLanguages.alias' => $this->lang18
                        ]
                    ]
                ]
            ],
            'order' => [
                'CidcClasses.id DESC'
            ],
        );
        $cidcClasses = $this->paginate($this->CidcClasses, array(
            'limit' => Configure::read('web.limit')
        ));

        $programs =  $this->CidcClasses->Programs->get_list($this->lang18);
        $courses = $this->CidcClasses->Courses->get_list($this->lang18);

        $session = $this->request->getSession();
        $administrator_id = $session->read('administrator.administrator_id');
        $centers = $this->CidcClasses->Centers->get_list_belong_administrator($this->lang18, $administrator_id);

        $status = $this->CidcClasses->get_status();
        $target_units = $this->CidcClasses->get_target_unit();

        $this->set(compact('cidcClasses', 'data_search', 'status', 'programs', 'courses', 'centers', 'target_units'));
    }

    private function check_belong_to_administrator($center_id)
    {
        $session = $this->request->getSession();
        $list_centers_management = $session->read('administrator.list_centers_management');

        if ($list_centers_management) {
            foreach ($list_centers_management as $cId) {
                if ($cId == $center_id) {
                    return true;
                }
            }
        }
       
        return false;
    }
    /**
     * View method
     *
     * @param string|null $id Cidc Class id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cidcClass = $this->CidcClasses->get($id, [
            'contain' => [
                'Programs' => [
                    'fields' => [
                        'Programs.id'
                    ],
                    'conditions' => [
                        'Programs.enabled' => true
                    ],
                    'ProgramLanguages' => [
                        'fields' => [
                            'ProgramLanguages.program_id',
                            'ProgramLanguages.name'
                        ],
                        'conditions' => [
                            'ProgramLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'Courses' => [
                    'fields' => [
                        'Courses.id'
                    ],
                    'conditions' => [
                        'Courses.enabled' => true
                    ],
                    'CourseLanguages' => [
                        'fields' => [
                            'CourseLanguages.course_id',
                            'CourseLanguages.name'
                        ],
                        'conditions' => [
                            'CourseLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'Centers' => [
                    'fields' => [
                        'Centers.id'
                    ],
                    'conditions' => [
                        'Centers.enabled' => true
                    ],
                    'CenterLanguages' => [
                        'fields' => [
                            'CenterLanguages.center_id',
                            'CenterLanguages.name'
                        ],
                        'conditions' => [
                            'CenterLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'ClassTypes' => [
                    'fields' => [
                        'ClassTypes.id'
                    ],
                    'conditions' => [
                        'ClassTypes.enabled' => true
                    ],
                    'ClassTypeLanguages' => [
                        'fields' => [
                            'ClassTypeLanguages.class_type_id',
                            'ClassTypeLanguages.name'
                        ],
                        'conditions' => [
                            'ClassTypeLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'CidcClassLanguages',
                'DateOfLessons' => [
                    'fields' => [
                        'DateOfLessons.day',
                        'DateOfLessons.cidc_class_id',
                    ],
                ],
                'CreatedBy',
                'ModifiedBy',
            ],
        ]);


        if (!$this->check_belong_to_administrator($cidcClass->center_id)) {
            $this->Flash->warning(__('incorrect_manage_data_permission'));
            return $this->redirect(['action' => 'index']);
        }

        $language_input_fields = array(
            'description'
        );
        $languages = $cidcClass->cidc_class_languages;
        $status = $this->CidcClasses->get_status();
        $target_units = $this->CidcClasses->get_target_unit();
        $date_for_lessons   = TableRegistry::get('DateOfLessons')->convert_date_of_lessons_to_string($cidcClass->date_of_lessons);
        $this->set(compact('cidcClass', 'status', 'languages', 'language_input_fields', "target_units", "date_for_lessons"));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'CidcClasses';

        $cidcClass = $this->CidcClasses->newEmptyEntity();
        if ($this->request->is('post')) {

            $db = $this->CidcClasses->getConnection();
            $db->begin();

            $data = $this->request->getData();
            $cidcClass = $this->CidcClasses->patchEntity($cidcClass, $data);
            $cidcClass['code'] = $this->CidcClasses->gen_code($data['center_id'], $data['class_type_id']);
            $cidc_class_language = $this->CidcClasses->CidcClassLanguages->newEntities($this->request->getData()['CidcClassLanguages']);

            if ($model = $this->CidcClasses->save($cidcClass)) {

                // 2, save language
                if (isset($cidc_class_language) && !empty($cidc_class_language)) {
                    foreach ($cidc_class_language as $language) {
                        $language['cidc_class_id'] = $model->id;
                    }
                    if (!$this->CidcClasses->CidcClassLanguages->saveMany($cidc_class_language)) {
                        $this->Flash->error(__('data_language_is_not_saved'));
                        goto load_data;
                    }
                }

                // save date_of_lesson
                if (isset($data['date_of_lessons']) && !empty($data['date_of_lessons'])) {
                    $date_of_lessons = [];
                    foreach ($data['date_of_lessons'] as $lesson) {
                        array_push($date_of_lessons, [
                            'cidc_class_id' => $model->id,
                            'day'           => $lesson
                        ]);
                    }
                    $data_insert = $this->CidcClasses->DateOfLessons->newEntities($date_of_lessons);
                    if (!$this->CidcClasses->DateOfLessons->saveMany($data_insert)) {
                        $this->Flash->error(__('data_date_of_lesson_is_not_saved'));
                        goto load_data;
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The CidcClass could not be saved. Please, try again.'));
            }
        }

        load_data:
        $status = $this->CidcClasses->get_status();
        $current_language = $this->lang18;
        $programs = $this->CidcClasses->Programs->get_list($current_language);
        $courses = $this->CidcClasses->Courses->get_list($current_language);

        $session = $this->request->getSession();
        $administrator_id = $session->read('administrator.id');
        $centers = $this->CidcClasses->Centers->get_list_belong_administrator($current_language, $administrator_id);

        $classTypes = $this->CidcClasses->ClassTypes->get_list($current_language);
        $target_units = $this->CidcClasses->get_target_unit();

        $weekends = $this->CidcClasses->get_weekend_day();
        $this->load_language();
        $holiday_lists = TableRegistry::get('CidcHolidays')->get_all_list();

        $holidays = [];
        foreach ($holiday_lists as $value) {
            $holidays[] = $value['date'];
        }
        $holidays = json_encode($holidays);

        $this->set(compact('weekends', 'cidcClass', 'current_language', 'programs', 'courses', 'centers', 'classTypes', 'status', 'target_units', 'holidays'));
    }

    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'description'
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'CidcClassLanguages';
        $languages_edit_model = 'cidc_class_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cidc Class id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {

        $cidcClass = $this->CidcClasses->get($id, [
            'contain' => [
                'CidcClassLanguages' => [],
                'Programs' => [
                    'fields' => [
                        'Programs.id'
                    ],
                    'conditions' => [
                        'Programs.enabled' => true
                    ],
                    'ProgramLanguages' => [
                        'fields' => [
                            'ProgramLanguages.program_id',
                            'ProgramLanguages.name'
                        ],
                        'conditions' => [
                            'ProgramLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'Courses' => [
                    'fields' => [
                        'Courses.id'
                    ],
                    'conditions' => [
                        'Courses.enabled' => true
                    ],
                    'CourseLanguages' => [
                        'fields' => [
                            'CourseLanguages.course_id',
                            'CourseLanguages.name'
                        ],
                        'conditions' => [
                            'CourseLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'Centers' => [
                    'fields' => [
                        'Centers.id'
                    ],
                    'conditions' => [
                        'Centers.enabled' => true
                    ],
                    'CenterLanguages' => [
                        'fields' => [
                            'CenterLanguages.center_id',
                            'CenterLanguages.name'
                        ],
                        'conditions' => [
                            'CenterLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'ClassTypes' => [
                    'fields' => [
                        'ClassTypes.id'
                    ],
                    'conditions' => [
                        'ClassTypes.enabled' => true
                    ],
                    'ClassTypeLanguages' => [
                        'fields' => [
                            'ClassTypeLanguages.class_type_id',
                            'ClassTypeLanguages.name'
                        ],
                        'conditions' => [
                            'ClassTypeLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'DateOfLessons' => [
                    'fields' => [
                        'DateOfLessons.day',
                        'DateOfLessons.cidc_class_id'
                    ],
                    'conditions' => [
                        'DateOfLessons.enabled' => true
                    ]
                ]
            ],
        ]);

        if (
            $cidcClass->status == $this->CidcClasses->PUBLISHED ||
            $cidcClass->status == $this->CidcClasses->COMPLETED
        ) {
            $this->Flash->success(__d('cidcclass', 'class_already_published_cannot_edit'));
            return $this->redirect(['action' => 'index']);
        }

        $languages_edit_data = (isset($cidcClass['cidc_class_languages']) && !empty($cidcClass['cidc_class_languages'])) ? $cidcClass['cidc_class_languages'] : false;

        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) {

            $db  = $this->CidcClasses->getConnection();
            $db->begin();

            // check min max students
            $_data = $this->request->getData();

            $obj_StudentRegisterClasses = TableRegistry::get('StudentRegisterClasses');
            $count = $obj_StudentRegisterClasses->find('all', [
                'conditions' => [
                    'StudentRegisterClasses.cidc_class_id' => $id
                ],
                'fields' => [
                    'StudentRegisterClasses.kid_id',
                ],
            ])->count();

            if (!($count <= $_data['maximum_of_students'] && $_data['minimum_of_students'] <= $_data['minimum_of_students'])) {
                $this->Flash->warning(__d('cidcclass', 'cannot_adjust_maximum_of_students_less_than_exist_student_join', $count));
                goto load_data;
            }

            $cidcClass = $this->CidcClasses->patchEntity($cidcClass, $_data);
            $data = $this->request->getData();
            if ($model = $this->CidcClasses->save($cidcClass)) {

                $obj_Holidays = TableRegistry::get('CidcHolidays');

                // edit-date-of-lesson
                if (isset($data['date_of_lessons']) && !empty($data['date_of_lessons'])) {
                    $this->CidcClasses->DateOfLessons->deleteAll([
                        'cidc_class_id' => $id
                    ]);
                    $date_of_lessons = [];
                    foreach ($data['date_of_lessons'] as $lesson) {
                        array_push($date_of_lessons, [
                            'cidc_class_id' => $model->id,
                            'day'           => $lesson
                        ]);
                    }
                    $data_insert = $this->CidcClasses->DateOfLessons->newEntities($date_of_lessons);
                    if (!$this->CidcClasses->DateOfLessons->saveMany($data_insert)) {
                        $this->Flash->error(__('data_date_lesson_is_not_saved'));
                        goto load_data;
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved')); // student had attended classes

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The cidcClass could not be saved. Please, try again.'));
            }
        }

        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $status = $this->CidcClasses->get_status();
        $programs = $this->CidcClasses->Programs->get_list($current_language);
        $courses = $this->CidcClasses->Courses->get_list($current_language);

        $session = $this->request->getSession();
        $administrator_id = $session->read('administrator.id');
        $centers = $this->CidcClasses->Centers->get_list_belong_administrator($current_language, $administrator_id);

        $classTypes = $this->CidcClasses->ClassTypes->get_list($current_language);
        $target_units = $this->CidcClasses->get_target_unit();

        $weekends = $this->CidcClasses->get_weekend_day();
        $holiday_lists = TableRegistry::get('CidcHolidays')->get_all_list();

        $holidays = [];
        foreach ($holiday_lists as $value) {
            $holidays[] = $value['date'];
        }
        $holidays = json_encode($holidays);

        $this->set(compact('holidays', 'weekends', 'cidcClass', 'current_language', 'programs', 'courses', 'centers', 'classTypes', 'languages_edit_data', 'status', 'target_units'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Cidc Class id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->check_manage_data_permission_action($id);

        $this->request->allowMethod(['post', 'delete']);
        $cidcClass = $this->CidcClasses->get($id);
        if ($this->CidcClasses->delete($cidcClass)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The cidc class could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
