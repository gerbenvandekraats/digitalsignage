<?php

    class DigitalSignageBroadcastSchedulesUpdateProcessor extends modObjectUpdateProcessor {
        /**
         * @access public.
         * @var String.
         */
        public $classKey = 'DigitalSignagePlayersSchedules';

        /**
         * @access public.
         * @var Array.
         */
        public $languageTopics = array('digitalsignage:default');

        /**
         * @access public.
         * @var String.
         */
        public $objectType = 'digitalsignage.schedules';

        /**
         * @access public.
         * @var Object.
         */
        public $digitalsignage;

        /**
         * @acces public.
         * @return Mixed.
         */
        public function initialize() {
            $this->digitalsignage = $this->modx->getService('digitalsignage', 'DigitalSignage', $this->modx->getOption('digitalsignage.core_path', null, $this->modx->getOption('core_path').'components/digitalsignage/').'model/digitalsignage/');

            if ('' === $this->getProperty('start_time')) {
                $this->setProperty('start_time', '00:00:00');
            }

            if ('' === $this->getProperty('start_date')) {
                $this->setProperty('start_date', '0000-00-00');
            }

            if ('' === $this->getProperty('end_time')) {
                $this->setProperty('end_time', '00:00:00');
            }

            if ('' === $this->getProperty('end_date')) {
                $this->setProperty('end_date', '0000-00-00');
            }

            return parent::initialize();
        }

        /**
         * @access public.
         * @return Mixed.
         */
        public function beforeSave() {
            if ('0000-00-00' !== ($date = $this->getProperty('start_date'))) {
                $this->object->set('start_date', date('Y-m-d', strtotime($date)));
            } else {
                $this->object->set('start_date', $date);
            }

            if ('0000-00-00' !== ($date = $this->getProperty('end_date'))) {
                $this->object->set('end_date', date('Y-m-d', strtotime($date)));
            } else {
                $this->object->set('end_date', $date);
            }

            if ($this->object->is('day')) {
                $start = array(
                    'date'	=> date('Y-m-d', strtotime('Next '.$this->object->getDayOfWeek())),
                    'time'	=> $this->getProperty('start_time')
                );

                $end = array(
                    'date'	=> date('Y-m-d', strtotime('Next '.$this->object->getDayOfWeek())),
                    'time'	=> $this->getProperty('end_time')
                );

                foreach ($this->getSchedules('day') as $schedule) {
                    if ($schedule->isScheduledFor($start, $end)) {
                        $this->addFieldError('type', $this->modx->lexicon('digitalsignage.error_broadcast_schedule_exists', array(
                            'schedule' => $schedule->toString()
                        )));

                        break;
                    }
                }
            } else {
                $start = array(
                    'date'	=> $this->getProperty('start_date'),
                    'time'	=> $this->getProperty('start_time')
                );

                $end = array(
                    'date'	=> $this->getProperty('end_date'),
                    'time'	=> $this->getProperty('end_time')
                );

                foreach ($this->getSchedules('date') as $schedule) {
                    if ($schedule->isScheduledFor($start, $end)) {
                        $this->addFieldError('type', $this->modx->lexicon('digitalsignage.error_broadcast_schedule_exists', array(
                            'schedule' => $schedule->toString()
                        )));

                        break;
                    }
                }
            }

            return parent::beforeSave();
        }

        /**
         * @access public.
         * @param String $value.
         * @return Array.
         */
        public function getSchedules($type) {
            return $this->modx->getCollection('DigitalSignagePlayersSchedules', array(
                'id:!='		=> $this->object->id,
                'player_id'	=> $this->getProperty('player_id'),
                'type' 		=> $type
            ));
        }
    }

    return 'DigitalSignageBroadcastSchedulesUpdateProcessor';

?>