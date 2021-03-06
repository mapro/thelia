<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Tinymce\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Cache\CacheEvent;
use Thelia\Core\Thelia;


/**
 * Class VerifyTinymceListener
 * @package Tinymce\Listener
 * @author Manuel Raynaud <mraynaud@openstudio.fr>
 */
class VerifyTinymceListener implements EventSubscriberInterface
{

    public function verifyTinymce(Event $event)
    {
        $fs = new Filesystem();
        if (false === file_exists(THELIA_WEB_DIR . '/tinymce')) {
            $fs->mirror(__DIR__ . DS .'..'. DS .'Resources'.DS.'js'.DS.'tinymce', THELIA_WEB_DIR . 'tinymce');
        }

        if (false === file_exists(THELIA_WEB_DIR . '/media')) {
            $fs->symlink(__DIR__ . DS .'..'.DS.'Resources'.DS.'media', THELIA_WEB_DIR . 'media');
        }
    }

    public function clearCache(CacheEvent $event)
    {
        if (true === file_exists(THELIA_WEB_DIR . '/tinymce')) {
            $fs = new Filesystem();

            $directory = new \DirectoryIterator(THELIA_WEB_DIR . '/tinymce');

            $fs->remove($directory);
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::BOOT => array('verifyTinymce', 128),
            TheliaEvents::CACHE_CLEAR => array("clearCache", 0)
        );
    }
}