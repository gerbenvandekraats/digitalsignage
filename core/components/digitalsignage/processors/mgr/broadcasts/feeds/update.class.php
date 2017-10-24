<?php

    class DigitalSignageBroadcastFeedsUpdateProcessor extends modObjectUpdateProcessor {
        /**
         * @access public.
         * @var String.
         */
        public $classKey = 'DigitalSignageBroadcastsFeeds';

        /**
         * @access public.
         * @var Array.
         */
        public $languageTopics = array('digitalsignage:default');

        /**
         * @access public.
         * @var String.
         */
        public $objectType = 'digitalsignage.broadcasts';

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

            if (null === $this->getProperty('published')) {
                $this->setProperty('published', 0);
            }

            return parent::initialize();
        }

        /**
         * @access public.
         * @return Mixed.
         */
        public function beforeSave() {
            if (!preg_match('/^(http|https)/si', $this->getProperty('url'))) {
                $this->setProperty('url', 'http://'.$this->getProperty('url'));
            }

            return parent::beforeSave();
        }

        /**
         * @access public.
         * @return Mixed.
         */
        public function afterSave() {
            $c = array(
                'id' => $this->object->broadcast_id
            );

            if (null !== ($broadcast = $this->modx->getObject('DigitalSignageBroadcasts', $c))) {
                $broadcast->fromArray(array(
                    'hash' => time()
                ));

                $broadcast->save();
            }

            return parent::afterSave();
        }
    }

    return 'DigitalSignageBroadcastFeedsUpdateProcessor';

?>