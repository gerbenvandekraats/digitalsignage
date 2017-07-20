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
	 
	class NarrowcastingSlidesTypesGetListProcessor extends modObjectGetListProcessor {
		/**
		 * @access public.
		 * @var String.
		 */
		public $classKey = 'NarrowcastingSlidesTypes';
		
		/**
		 * @access public.
		 * @var Array.
		 */
		public $languageTopics = array('narrowcasting:default', 'narrowcasting:slides');
		
		/**
		 * @access public.
		 * @var String.
		 */
		public $defaultSortField = 'key';
		
		/**
		 * @access public.
		 * @var String.
		 */
		public $defaultSortDirection = 'ASC';
		
		/**
		 * @access public.
		 * @var String.
		 */
		public $objectType = 'narrowcasting.slidestypes';
		
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
			
			return parent::initialize();
		}
		
		/**
		 * @access public.
		 * @param Object $object.
		 * @return Array.
		 */
		public function prepareRow(xPDOObject $object) {
			$array = array_merge($object->toArray(), array(
				'name_formatted' 			=> $object->name,
				'description_formatted' 	=> $object->description,
				'data'						=> unserialize($object->data)
			));
			
			if (empty($object->name)) {
				$translationKey = 'narrowcasting.slide_'.$object->key;
				
				if ($translationKey !== ($translation = $this->modx->lexicon($translationKey))) {
					$array['name_formatted'] = $translation;
				}
			}
			
			if (empty($object->description)) {
				$translationKey = 'narrowcasting.slide_'.$object->key.'_desc';
				
				if ($translationKey !== ($translation = $this->modx->lexicon($translationKey))) {
					$array['description_formatted'] = $translation;
				}
			}

			return $array;
		}
	}

	return 'NarrowcastingSlidesTypesGetListProcessor';
	
?>