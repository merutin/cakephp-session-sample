<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * SessionTest Controller
 *
 * Test controller to verify Redis session functionality
 */
class SessionTestController extends AppController
{
    /**
     * Index method
     * Displays and increments session counter
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $session = $this->request->getSession();
        
        // Debug: Check session configuration
        $sessionConfig = \Cake\Core\Configure::read('Session');
        
        // Get current count from session
        $count = $session->read('visit_count');
        if ($count === null) {
            $count = 0;
        }
        
        // Increment count
        $count++;
        
        // Save to session
        $session->write('visit_count', $count);
        
        // Verify write
        $verifyCount = $session->read('visit_count');
        
        // Get session ID
        $sessionId = $session->id();
        
        // Get all session data for debugging
        $allData = $session->read();
        
        $this->set(compact('count', 'sessionId', 'sessionConfig', 'verifyCount', 'allData'));
    }
}
