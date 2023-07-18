<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\ORM\TableRegistry;
use App\Controller\Admin\AppController;
use Cake\Core\Configure;


/**
 * PrivacyPolicies Controller
 *
 * @method \App\Model\Entity\PrivacyPolicy[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PrivacyPoliciesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $data_search = $this->request->getQuery();
        $_conditions =  array();

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['Programs.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['title']) && !empty($data_search['title'])) {
            $_conditions['LOWER(PrivacyPolicyLanguages.title) LIKE'] = '%' . trim(strtolower($data_search['title'])) . '%';
        }
        $this->paginate = [
            'fields' => [
                'PrivacyPolicies.id',
                'PrivacyPolicies.enabled',
                'PrivacyPolicies.created',
                'PrivacyPolicies.modified',
                'PrivacyPolicyLanguages.title',
            ],
            'conditions' => $_conditions,
            'order'     => [
                'PrivacyPolicies.id DESC'
            ],
            'join'  => [
                'table' => 'privacy_policy_languages',
                'alias' => 'PrivacyPolicyLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'PrivacyPolicyLanguages.privacy_policy_id = PrivacyPolicies.id',
                    'PrivacyPolicyLanguages.alias' => $this->lang18,
                ],
            ]
        ];

        $privacyPolicies = $this->paginate($this->PrivacyPolicies, [
            'limit' => Configure::read('web.limit')
        ]); 
        $this->set(compact('privacyPolicies', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Privacy Policy id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $privacyPolicy = $this->PrivacyPolicies->get($id, [
            'contain' => [
                'CreatedBy',
                'ModifiedBy',
                'PrivacyPolicyLanguages'
            ],
        ]);
        $language_input_fields = array(
            'title',
            'content'
        );
        $languages = $privacyPolicy->privacy_policy_languages;
        $this->set(compact('privacyPolicy', 'language_input_fields', 'languages'));
    }

    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'title',
            'content',
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'PrivatePolicyLanguages';
        $languages_edit_model = 'privacy_policy_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function _add()
    {
        $privacyPolicy = $this->PrivacyPolicies->newEmptyEntity();
        if ($this->request->is('post')) {
            $privacyPolicy = $this->PrivacyPolicies->patchEntity($privacyPolicy, $this->request->getData());
            $db = $this->PrivacyPolicies->getConnection();
            $db->begin();

            $privacy_policy_languages = $this->PrivacyPolicies->PrivacyPolicyLanguages->newEntities($this->request->getData()['PrivatePolicyLanguages']);

            if ($model = $this->PrivacyPolicies->save($privacyPolicy)) {
                if (isset($privacy_policy_languages) && !empty($privacy_policy_languages)) {
                    foreach ($privacy_policy_languages as $language) {
                        $language['privacy_policy_id'] = $model->id;
                    }
                    if (!$this->PrivacyPolicies->PrivacyPolicyLanguages->saveMany($privacy_policy_languages)) {
                        $db->rollback();
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $db->rollback();
            $this->Flash->error(__('The privacy policy could not be saved. Please, try again.'));
        }
        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->set(compact('privacyPolicy'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Privacy Policy id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $privacyPolicy = $this->PrivacyPolicies->get($id, [
            'contain' => [
                'PrivacyPolicyLanguages'
            ],
        ]);
        $languages_edit_data = (isset($privacyPolicy['privacy_policy_languages']) && !empty($privacyPolicy['privacy_policy_languages'])) ? $privacyPolicy['privacy_policy_languages'] : false;
        if ($this->request->is(['patch', 'post', 'put'])) {
            $privacyPolicy = $this->PrivacyPolicies->patchEntity($privacyPolicy, $this->request->getData());
            $db = $this->PrivacyPolicies->getConnection();
            $db->begin();
            if ($this->PrivacyPolicies->save($privacyPolicy)) {

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The policy could not be saved. Please, try again.'));
            }
        }
        load_data:
        $this->load_language();
        $current_language = $this->lang18;
        $this->set(compact('privacyPolicy', 'current_language', 'languages_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Privacy Policy id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $privacyPolicy = $this->PrivacyPolicies->get($id, [
            'PrivacyPolicyLanguages'
        ]);
        if ($this->PrivacyPolicies->delete($privacyPolicy)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The privacy policy could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
