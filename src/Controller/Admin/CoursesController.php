<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * Courses Controller
 *
 * @method \App\Model\Entity\Course[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CoursesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $data_search = $this->request->getQuery();
        $units = $this->Courses->get_unit();
        $_conditions =  array();

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['Courses.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['program_id']) && $data_search['program_id'] != "") {
            $_conditions['Courses.program_id'] = $data_search['program_id'];
        }

        if (isset($data_search['unit']) && $data_search['unit'] != "") {
            $_conditions['Courses.unit'] = $data_search['unit'];
        }

        if (isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(CourseLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'Courses.id',
                'Courses.program_id',
                'Courses.sort',
                'Courses.age_range_from',
                'Courses.age_range_to',
                'Courses.unit',
                'Courses.enabled',
                'Courses.created',
                'Courses.modified',
                'CourseLanguages.name'
            ],
            'conditions' => $_conditions,
            'contain'   => [
                'Programs' => [
                    'fields' => [
                        'Programs.id'
                    ],
                    'conditions' => [
                        'Programs.enabled' =>  true
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
                ]
            ],
            'order' => [
                'Courses.program_id DESC'
            ],
            'join' => [
                'table' => 'course_languages',
                'alias' => 'CourseLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'CourseLanguages.course_id = Courses.id',
                    'CourseLanguages.alias' => $this->lang18,
                ],
            ]
        );
        $courses = $this->paginate($this->Courses, array(
            'limit' => Configure::read('web.limit')
        ));
        
        $programs = $this->Courses->Programs->get_list($this->lang18);
        $this->set(compact('courses', 'data_search', 'units', 'programs'));
    }

    /**
     * View method
     *
     * @param string|null $id Course id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $course = $this->Courses->get($id, [
            'contain' => [
                'CreatedBy',
                'ModifiedBy',
                'CourseLanguages',
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
                ]
            ],
        ]);
        $language_input_fields = array(
            'name',
            'description'
        );
        $languages = $course->program_languages;
        $units = $this->Courses->get_unit();
        $this->set(compact('course', 'language_input_fields', 'languages', 'units'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'Courses';
        $course = $this->Courses->newEmptyEntity();
        $units = $this->Courses->get_unit();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            // debug($data);
            $db = $this->Courses->getConnection();
            $db->begin();

            $course = $this->Courses->patchEntity($course, $this->request->getData());
            // debug($course);exit;
            $course_language = $this->Courses->CourseLanguages->newEntities($this->request->getData()['CourseLanguages']);

            if ($model = $this->Courses->save($course)) {

                // 2, save language
                if (isset($course_language) && !empty($course_language)) {
                    foreach ($course_language as $language) {
                        $language['course_id'] = $model->id;
                    }
                    if (!$this->Courses->CourseLanguages->saveMany($course_language)) {
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The cours$course could not be saved. Please, try again.'));
            }
        }
        load_data:
        $current_language = $this->lang18;
        $programs = $this->Courses->Programs->get_list($current_language);
        $this->load_language();
        $this->set(compact('course', 'programs', 'current_language', 'units'));
    }

    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'name'
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'CourseLanguages';
        $languages_edit_model = 'course_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Course id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $course = $this->Courses->get($id, [
            'contain' => [
                'CourseLanguages',
            ],
        ]);
        $units = $this->Courses->get_unit();
        $languages_edit_data = (isset($course['course_languages']) && !empty($course['course_languages'])) ? $course['course_languages'] : false;

        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) {

            $db  = $this->Courses->getConnection();
            $db->begin();

            $course = $this->Courses->patchEntity($course, $this->request->getData());

            if ($this->Courses->save($course)) {

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The course could not be saved. Please, try again.'));
            }
        }

        load_data:
        $this->load_language();
        $current_language = $this->lang18;
        $programs = $this->Courses->Programs->get_list($current_language);
        $this->set(compact('course', 'current_language', 'languages_edit_data', 'programs', 'units'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Course id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']); 

        // Check exist in class
        $exist = $this->loadModel('CidcClasses')->exists(['course_id' => $id]);
        if ($exist) {
            $this->Flash->warning(__d('cidcclass', 'cannot_delete_it_because_this_course_already_exist_on_class'));
            goto return_data;
        } 
        
        $course = $this->Courses->get($id);
        if ($this->Courses->delete($course)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The course could not be deleted. Please, try again.'));
        }

        return_data:
        return $this->redirect(['action' => 'index']);
    }
}
