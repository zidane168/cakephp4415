<?php
declare(strict_types=1);
 
namespace App\Controller;

use Cake\Routing\Router;

/**
 * Landings Controller
 *
 * @method \App\Model\Entity\Landing[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LandingsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // $landings = $this->paginate($this->Landings);

        // $this->set(compact('landings'));
        $host_name = Router::url('/', true);
        $this->set(compact('host_name'));
    }

    public function menu2() {

    }
}
