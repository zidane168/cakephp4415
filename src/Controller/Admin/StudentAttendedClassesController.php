<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use App\MyHelper\MyHelper;
use Cake\ORM\TableRegistry;

/**
 * StudentAttendedClasses Controller
 *
 * @property \App\Model\Table\StudentAttendedClassesTable $StudentAttendedClasses
 * @method \App\Model\Entity\StudentAttendedClass[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StudentAttendedClassesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($id)
    { 
        // get cidc classes info 
        $obj_cidcClasses = TableRegistry::get('CidcClasses');
        $cidcClass = $obj_cidcClasses->get_by_id_student_attend_class($id);

        // get student attended classes info
        $studentAttendedClasses = $this->StudentAttendedClasses->find('all', [
            'conditions' => [
                'StudentAttendedClasses.cidc_class_id' => $id,
                'StudentAttendedClasses.is_completed' => 0,     // dang va se hoc
            ],
            'contain' => [
                'Kids' => [
                    'fields' => [
                        'Kids.id',
                    ],
                    'KidLanguages' => [
                        'conditions' => [
                            'KidLanguages.alias' => $this->lang18,
                        ],
                    ],
                ],
            ],
            'fields' => [
                'StudentAttendedClasses.id',
                'StudentAttendedClasses.date',
                'StudentAttendedClasses.status',
            ],
        ]);

        // format data
        $students = [];
        $dates = [];
        foreach ($studentAttendedClasses as $value) {
            $students[$value->kid->id][] = [
                'id' => $value->kid->id,
                'name' => $value->kid->kid_languages[0]->name,
                'date' => $value->date->format('Y-m-d'),
                'status' => $value->status,     // attended, absent, AL
            ];
            $dates[] =  $value->date->format('Y-m-d');
        }

        // arrange and joins date together (for circle class) 
        $dates = array_unique($dates); 
        asort($dates);  

        $cidc_class_id = $id;

        $this->set(compact('dates', 'cidcClass', 'students', 'cidc_class_id'));     
    }

    /**
     * View method
     *
     * @param string|null $id Student Attended Class id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $studentAttendedClass = $this->StudentAttendedClasses->get($id, [
            'contain' => ['CidcClasses', 'Kids', 'CreatedBy', 'ModifiedBy'],
        ]);

        $this->set(compact('studentAttendedClass'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $studentAttendedClass = $this->StudentAttendedClasses->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $exist_data = $this->StudentAttendedClasses->find('all', [
                'conditions' => [
                    'StudentAttendedClasses.kid_id' => $data['kid_id'],
                    'StudentAttendedClasses.cidc_class_id' => $data['cidc_class_id'],
                    'StudentAttendedClasses.date'          => $data['date_id']
                ]
            ])->first();
            if ($exist_data) {
                $this->Flash->error(__('This student is register this class on this date'));
                goto set_result;
            }
            $studentAttendedClass = $this->StudentAttendedClasses->patchEntity($studentAttendedClass, $this->request->getData());
            $studentAttendedClass->date = $data['date_id'];
            if ($this->StudentAttendedClasses->save($studentAttendedClass)) {
                $this->Flash->success(__('data_is_saved'));
                return $this->redirect(['action' => 'index', $data['cidc_class_id']]);
            }
            $this->Flash->error(__('The student attended class could not be saved. Please, try again.'));
        }
        set_result:
        $cidcClasses = $this->StudentAttendedClasses->CidcClasses->get_list($this->lang18, [
            'CidcClasses.enabled' => true,
            'CidcClasses.status'  => $this->StudentAttendedClasses->CidcClasses->PUBLISHED,
        ]);
        $kids = $this->StudentAttendedClasses->Kids->get_list($this->lang18);
        $current_language = $this->lang18;
        $this->set(compact('studentAttendedClass', 'cidcClasses', 'kids', 'current_language'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Student Attended Class id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($kid_id = null, $cidc_class_id = null)
    {
        $listStudentAttendedClass = $this->StudentAttendedClasses->find(
            'all',
            [
                'conditions' => [
                    'StudentAttendedClasses.kid_id' => $kid_id,
                    'StudentAttendedClasses.cidc_class_id' => $cidc_class_id
                ]
            ]
        )->toArray();
        $days = [];
        foreach ($listStudentAttendedClass as $item) {
            array_push($days, date_format($item->date, 'Y-m-d'));
        }
        $studentAttendedClass = $listStudentAttendedClass[0];
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            // debug($days);exit;
            if (!in_array($days[$data['date']], $days)) {
                $this->Flash->error(__('wrong_date'));
            }
            $saveData = $this->StudentAttendedClasses->find('all', [
                'conditions' => [
                    'kid_id' => $kid_id,
                    'cidc_class_id' => $cidc_class_id,
                    'date'  => $days[$data['date']]
                ]
            ])->first();
            $saveData->status = $data['status'];
            if ($this->StudentAttendedClasses->save($saveData)) {
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index', $cidc_class_id]);
            }
            $this->Flash->error(__('The student attended class could not be saved. Please, try again.'));
        }
        $cidcClasses = $this->StudentAttendedClasses->CidcClasses->get($cidc_class_id);
        $kids = $this->StudentAttendedClasses->Kids->get(
            $kid_id,
            ['contain' => ['KidLanguages']]
        );
        $status = $this->StudentAttendedClasses->get_status();
        $this->set(compact('cidcClasses', 'kids', 'status', 'studentAttendedClass', 'days'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Student Attended Class id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentAttendedClass = $this->StudentAttendedClasses->get($id);
        if ($this->StudentAttendedClasses->delete($studentAttendedClass)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The student attended class could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
