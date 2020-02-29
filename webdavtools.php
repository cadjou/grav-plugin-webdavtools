<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Plugin;
use Sabre\DAV\Client;

/**
 * Class WebdavtoolsPlugin
 * @package Grav\Plugin
 */
class WebdavtoolsPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            // ['autoload', 100000], // TODO: Remove when plugin requires Grav >=1.7
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
    * Composer autoload.
    *is
    * @return ClassLoader
    */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main events we are interested in
        $this->enable([
            // Put your main events here
        ]);
    }
    public static function dataProcess($content)
    {
        require __DIR__ . '/vendor/autoload.php';
        $settings = array(
            'baseUri' => 'https://nuage.kwa.agency/remote.php/webdav/',
            'userName' => 'cdavid',
            'password' => 'YLa8_4v83pF',
        );

        $client = new Client($settings);
        $client->addCurlSetting(CURLOPT_SSL_VERIFYPEER, false);
        $url = 'Groupe KWA/Comptes Clients/Comptes Clients France/Zahia - 517';
        $encode = [];
        foreach(explode('/',$url) as $valeur)
        {
            $encode[] = rawurlencode($valeur);
        }
        $url = '/remote.php/webdav/' . implode('/',$encode) . '/testfact_' . time() . '.pdf';
        // print_r($url);
        $response = $client->request('PUT', $url, $content);
        $response = $client->propfind($url, array(
    '{DAV:}displayname',
    // '{DAV:}getlastmodified',
    // '{DAV:}getetag',
    // '{DAV:}getcontenttype',
    // '{DAV:}resourcetype',
    // '{DAV:}getcontentlength',
    // '{http://owncloud.org/ns}id',
    '{http://owncloud.org/ns}fileid',
    // '{http://owncloud.org/ns}favorite',
    // '{http://owncloud.org/ns}comments-href',
    // '{http://owncloud.org/ns}comments-count',
    // '{http://owncloud.org/ns}comments-unread',
    // '{http://owncloud.org/ns}owner-id',
    // '{http://owncloud.org/ns}owner-display-name',
    // '{http://owncloud.org/ns}share-types',
    // '{http://owncloud.org/ns}checksums',
    // '{http://nextcloud.org/ns}has-preview',
    // '{http://owncloud.org/ns}size',
),1);
        // print_r($response);
        foreach($response as $pathUrl=>$data)
        {
            if ($url == $pathUrl)
            {
                $fileid = $data['{http://owncloud.org/ns}fileid'];
                break;
            }
        }
        if (!$fileid)
        {
            exit;
        }
        $data = '{"userVisible":true,"userAssignable":true,"canAssign":true,"id":"2","name":"A PAYER"}'; 
        // print_r($features);
        $ch = curl_init();
        
        $url = 'https://nuage.kwa.agency/remote.php/dav/systemtags-relations/files/' . $fileid . '/2';
        // print_r($url);
        curl_setopt($ch, CURLOPT_URL,$url);  
        curl_setopt($ch, CURLOPT_USERPWD, 'cdavid' . ':' . 'YLa8_4v83pF');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        $headers = [];
        $headers[] = 'Connection: keep-alive';
        $headers[] = 'Content-Length: ' . strlen($data);
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // In real life you should use something like:
                 // http_build_query(array('postvar1' => 'value1')));

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);
        // print_r($server_output);

        exit;
	}
}
