<?php
App::uses('AppController', 'Controller');
/**
 * ErrorLogs Controller
 *
 * @property ErrorLog $ErrorLog
 * @property PaginatorComponent $Paginator
 * @property nComponent $n
 */
class ErrorLogsController extends AppController {

/**
 * Compon`ents
 *
 * @var array
 */
	public $components = array('Paginator');

	public $paginate = array(
        'limit' => 100,
        'order' => array(
            'ErrorLog.id' => 'desc'
        )
    );

    function beforeRender() {
		$this->set("error_log_url", Router::url(array('controller' => 'ErrorLogs', 'action' => 'index')));
    }
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->set("page_title", __("Error Logs"));
		$this->ErrorLog->recursive = 0;
		$this->Paginator->settings = $this->paginate;
		$filters = $_GET;
		if(isset($filters["status"])) {
			$filters["status"] = intval($filters["status"]);
			$conditions["status"] = $filters["status"];
		}

		if(!empty($conditions)) {
			$this->Paginator->settings["conditions"] = $conditions;
		}
		$this->set('ErrorLogs', $this->Paginator->paginate());
		$this->set('filters', $filters);
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ErrorLog->exists($id)) {
			throw new NotFoundException(__('Invalid gb error log'));
		}
		$options = array('conditions' => array('ErrorLog.' . $this->ErrorLog->primaryKey => $id));
		$this->set('ErrorLog', $this->ErrorLog->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ErrorLog->create();
			if ($this->ErrorLog->save($this->request->data)) {
				return $this->flash(__('The gb error log has been saved.'), array('action' => 'index'));
			}
		}
		$statements = $this->ErrorLog->Statement->find('list');
		$this->set(compact('statements'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->ErrorLog->exists($id)) {
			throw new NotFoundException(__('Invalid gb error log'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ErrorLog->save($this->request->data)) {
				return $this->flash(__('The gb error log has been saved.'), array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('ErrorLog.' . $this->ErrorLog->primaryKey => $id));
			$this->request->data = $this->ErrorLog->find('first', $options);
		}
		$statements = $this->ErrorLog->Statement->find('list');
		$this->set(compact('statements'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ErrorLog->id = $id;
		if (!$this->ErrorLog->exists()) {
			throw new NotFoundException(__('Invalid gb error log'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ErrorLog->delete()) {
			return $this->flash(__('The gb error log has been deleted.'), array('action' => 'index'));
		} else {
			return $this->flash(__('The gb error log could not be deleted. Please, try again.'), array('action' => 'index'));
		}
	}
}
