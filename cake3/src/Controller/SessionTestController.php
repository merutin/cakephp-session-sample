<?php
namespace App\Controller;

use Cake\Core\Configure;

/**
 * SessionTest Controller
 *
 * Test controller to verify Redis session functionality in CakePHP 3
 */
class SessionTestController extends AppController
{
    /**
     * Index method
     * Displays and increments session counter
     *
     * @return void
     */
    public function index()
    {
        $session = $this->request->session();
        
        // Debug: Check session configuration
        $sessionConfig = Configure::read('Session');
        
        // Get current count from session
        $count = $session->read('visit_count');
        if ($count === null) {
            $count = 0;
        }
        
        // Increment count
        $count++;
        
        // Save to session
        $session->write('visit_count', $count);
        
        // Store current timestamp
        $session->write('last_visit', date('Y-m-d H:i:s'));
        
        // Verify write
        $verifyCount = $session->read('visit_count');
        
        // Get session ID
        $sessionId = $session->id();
        
        // Get all session data for debugging
        $allData = $session->read();
        
        $this->set(compact('count', 'sessionId', 'sessionConfig', 'verifyCount', 'allData'));
    }

    /**
     * Clear method
     * Destroys the session and redirects to index
     *
     * @return \Cake\Http\Response
     */
    public function clear()
    {
        $this->request->session()->destroy();
        return $this->redirect(['action' => 'index']);
    }
}
