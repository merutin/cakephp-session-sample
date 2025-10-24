<?php
/**
 * Session test controller for testing Redis session storage
 */
App::uses('AppController', 'Controller');

class SessionTestController extends AppController {

	public function index() {
		// Set or retrieve session data
		if ($this->Session->check('visit_count')) {
			$count = $this->Session->read('visit_count');
			$count++;
			$this->Session->write('visit_count', $count);
		} else {
			$count = 1;
			$this->Session->write('visit_count', $count);
		}

		// Store current timestamp
		$this->Session->write('last_visit', date('Y-m-d H:i:s'));

		$this->set('visit_count', $count);
		$this->set('last_visit', $this->Session->read('last_visit'));
		$this->set('session_id', session_id());
	}

	public function clear() {
		$this->Session->destroy();
		$this->redirect(array('action' => 'index'));
	}
}
