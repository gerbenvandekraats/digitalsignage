<?php

	/**
	 * Narrowcasting
	 *
	 * Copyright 2016 by Oene Tjeerd de Bruin <oenetjeerd@sterc.nl>
	 *
	 * Narrowcasting is free software; you can redistribute it and/or modify it under
	 * the terms of the GNU General Public License as published by the Free Software
	 * Foundation; either version 2 of the License, or (at your option) any later
	 * version.
	 *
	 * Narrowcasting is distributed in the hope that it will be useful, but WITHOUT ANY
	 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
	 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License along with
	 * Narrowcasting; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
	 * Suite 330, Boston, MA 02111-1307 USA
	 */

	class NarrowcastingBroadcastsUpdateProcessor extends modObjectUpdateProcessor {
		/**
		 * @access public.
		 * @var String.
		 */
		public $classKey = 'NarrowcastingBroadcasts';
		
		/**
		 * @access public.
		 * @var Array.
		 */
		public $languageTopics = array('narrowcasting:default');
		
		/**
		 * @access public.
		 * @var String.
		 */
		public $objectType = 'narrowcasting.broadcasts';
		
		/**
		 * @access public.
		 * @var Object.
		 */
		public $narrowcasting;
		
		/**
		 * @acces public.
		 * @return Mixed.
		 */
		public function initialize() {
			$this->narrowcasting = $this->modx->getService('narrowcasting', 'Narrowcasting', $this->modx->getOption('narrowcasting.core_path', null, $this->modx->getOption('core_path').'components/narrowcasting/').'model/narrowcasting/');

			return parent::initialize();
		}
		
		/**
		 * @acces public.
		 * @return Mixed.
		 */
		public function beforeSave() {
		    $this->object->set('has', time());

			$response = $this->modx->runProcessor('resource/update', array(
				'id'			=> $this->getProperty('resource_id'),
				'pagetitle' 	=> $this->getProperty('name'),
				'description'	=> $this->getProperty('description'),
				'alias'			=> $this->getProperty('name'),
				'context_key'	=> $this->modx->getOption('narrowcasting.context'),
				'template'		=> $this->getProperty('template'),
				'show_in_tree'	=> 0
			));
			
			if ($response->isError()) {
				foreach ($response->getFieldErrors() as $error) {
					$this->addFieldError('name', $error->message);
				}
			}
			
			if (null !== ($object = $response->getObject())) {
				if (isset($object['id'])) {
					$this->object->set('resource_id', $object['id']);
				} else {
					$this->addFieldError('name', $this->modx->lexicon('error_resource_object'));
				}
			} else {
				$this->addFieldError('name', $this->modx->lexicon('error_resource_object'));
			}
			
			if (!preg_match('/^(http|https)/si', $this->getProperty('ticker_url'))) {
				$this->setProperty('url', 'http://'.$this->getProperty('ticker_url'));
			}

			return parent::beforeSave();
		}

        /**
         * @access public.
         * @return Mixed.
         */
        public function afterSave() {
            $this->modx->cacheManager->refresh(array(
                'db'                => array(),
                'auto_publish'      => array(
                    'contexts'          => array($this->modx->getOption('narrowcasting.context'))
                ),
                'context_settings'  => array(
                    'contexts'          => array($this->modx->getOption('narrowcasting.context'))
                ),
                'resource'          => array(
                    'contexts'          => array($this->modx->getOption('narrowcasting.context'))
                )
            ));

            $this->modx->call('modResource', 'refreshURIs', array(&$this->modx));

            return parent::afterSave();
        }
	}
	
	return 'NarrowcastingBroadcastsUpdateProcessor';
	
?>