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
	 
	class NarrowcastingBroadcastsGetListProcessor extends modObjectGetListProcessor {
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
		public $defaultSortField = 'id';
		
		/**
		 * @access public.
		 * @var String.
		 */
		public $defaultSortDirection = 'DESC';
		
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
		 * @access public.
		 * @return Mixed.
		 */
		public function initialize() {
			$this->narrowcasting = $this->modx->getService('narrowcasting', 'Narrowcasting', $this->modx->getOption('narrowcasting.core_path', null, $this->modx->getOption('core_path').'components/narrowcasting/').'model/narrowcasting/');
			
			$this->setDefaultProperties(array(
				'dateFormat' => $this->modx->getOption('manager_date_format') .', '. $this->modx->getOption('manager_time_format')
			));
			
			return parent::initialize();
		}
		
		/**
		 * @access public.
		 * @param Object $c.
		 * @return Object.
		 */
		public function prepareQueryBeforeCount(xPDOQuery $c) {
			$c->select($this->modx->getSelectColumns('NarrowcastingBroadcasts', 'NarrowcastingBroadcasts'));
			$c->select($this->modx->getSelectColumns('modResource', 'modResource', null, array('pagetitle', 'description', 'template')));
			
			$c->innerjoin('modResource', 'modResource', array('modResource.id = NarrowcastingBroadcasts.resource_id'));
			
			$query = $this->getProperty('query');
			
			if (!empty($query)) {
				$c->where(array(
					'modResource.pagetitle:LIKE' => '%'.$query.'%'
				));
			}
			
			return $c;
		}
		
		/**
		 * @access public.
		 * @param Object $query.
		 * @return Array.
		 */
		public function prepareRow(xPDOObject $object) {
			$array = array_merge($object->toArray(), array(
				'name'				=> $object->pagetitle,
				'name_formatted'	=> $object->pagetitle.($this->modx->hasPermission('tree_show_resource_ids') ? ' ('.$object->resource_id.')' : ''),
				'slides'			=> count($object->getSlides()),
				'feeds'				=> count($object->getFeeds()),
				'players' 			=> array(),
				'sync'				=> array(
					'valid'				=> !$object->needSync(),
					'timestamp'			=> $object->getLastSync()
				)
			));
			
			foreach ($object->getPlayers() as $player) {
				$array['players'][] = array(
					'key'		=> $player->key,
					'name'		=> $player->name,
					'online' 	=> $player->isOnline($object->id)
				);
			}
			
			if (in_array($array['sync']['timestamp'], array('-001-11-30 00:00:00', '-1-11-30 00:00:00', '0000-00-00 00:00:00', null))) {
				$array['sync']['timestamp'] = '';
			} else {
				$array['sync']['timestamp'] = date($this->getProperty('dateFormat'), strtotime($array['sync']['timestamp']));
			}
			
			if (in_array($array['editedon'], array('-001-11-30 00:00:00', '-1-11-30 00:00:00', '0000-00-00 00:00:00', null))) {
				$array['editedon'] = '';
			} else {
				$array['editedon'] = date($this->getProperty('dateFormat'), strtotime($array['editedon']));
			}
			
			return $array;
		}
	}

	return 'NarrowcastingBroadcastsGetListProcessor';
	
?>