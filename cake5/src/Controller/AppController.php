<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/5/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/5/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    /**
     * Before filter callback.
     * Update CakePHP 2 compatible Config data in session
     *
     * @param \Cake\Event\EventInterface $event The beforeFilter event.
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);
        
        $session = $this->request->getSession();
        
        // Maintain CakePHP 2 compatible Config data
        $config = $session->read('Config');
        
        if (!$config) {
            // Initialize Config if it doesn't exist
            $config = [];
        }
        
        // Update timestamp - CakePHP 2 uses this to check for session timeout
        // In CakePHP 2's default mode (_useForwardsCompatibleTimeout = false),
        // Config.time stores the expiration time (current_time + timeout)
        $timeout = Configure::read('Session.timeout', 120); // Default: 120 minutes
        $config['time'] = time() + ($timeout * 60);
        
        // Set userAgent if not set - CakePHP 2 uses this for session validation
        if (!isset($config['userAgent'])) {
            $userAgent = $this->request->getHeaderLine('User-Agent');
            $config['userAgent'] = md5($userAgent . Configure::read('Security.salt'));
        }
        
        // Set countdown if not set - CakePHP 2 uses this for auto-regeneration
        if (!isset($config['countdown'])) {
            $config['countdown'] = 10;
        }
        
        $session->write('Config', $config);
    }
}
