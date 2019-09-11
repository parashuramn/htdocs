<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	public $recursive = -1;
    function find($conditions = null, $fields = array(), $order = null, $recursive = null) {

	/* Solution for Pagination count with DISTINCT fields */ 
	/* Need to add a parameter countField in find calls e.g. */
	/* "countField" => "DISTINCT verb_id, objectid, agent_id" */
	if(is_string($conditions) && $conditions == "count" && !empty($fields["countField"]))
	{
		$fields["fields"] = $fields["countField"];
	}

	/* Caching Query method */
        $doQuery = true;
        // check if we want the cache
        if (!empty($fields['cache'])) {
            $cacheConfig = null;
            // check if we have specified a custom config
            if (!empty($fields['cacheConfig'])) {
                $cacheConfig = $fields['cacheConfig'];
            }
	    $settings = Cache::settings($cacheConfig);
	    if(empty($settings)) {
		Cache::config('_cake_short', array(
		    'engine' => 'File',
		    'duration'=> '+5 minutes',
		    'probability'=> 100,
		    'path' => CACHE . 'models' . DS,
		    'prefix' => Inflector::slug(APP_DIR) . '_'.'cache_short_'
		));
		$cacheConfig = '_cake_short';
	    }

            $cacheName = $this->name . '-' . User::get('id') . '-'  . $fields['cache'];
            // if so, check if the cache exists
            $data = Cache::read($cacheName, $cacheConfig);

            if ($data == false) {
		if(!empty($fields['cacheDuration'])) {
			Cache::set(array("duration" => $fields['cacheDuration']), $cacheConfig);
		}	
                $data = parent::find($conditions, $fields,
                    $order, $recursive);

                Cache::write($cacheName, $data, $cacheConfig);
		
		if(!empty($fields['cacheDuration'])) {
			Cache::set(array("duration" => $settings['duration']), $cacheConfig);
		}
            }
            $doQuery = false;
        }
        if ($doQuery) {
            $data = parent::find($conditions, $fields, $order,
                $recursive);
        }
        return $data;
    }
}
