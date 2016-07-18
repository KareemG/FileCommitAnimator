<?php namespace FileCommitAnimator;

use JonnyW\PhantomJs\Client;

/**
 * Creates screenshots of content at URL.
 * 
 * @version 1.0
 * @link https://github.com/KareemG/FileCommitAnimator
 * @author Kareem Golaub <kareemag@live.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Kareem Golaub
 */
class ScreenshotCreator {
    
    function __construct($binPath) {
        $this->binPath = $binPath;
        $this->client = Client::getInstance();
        $this->client->getEngine()->setPath($binPath);
    }
    
    /**
     * Creates a centered screenshot of content at URL. 
     *
     * @param string $url URL of content.
     * @param int $width Width of output image.
     * @param int $height Height of output image.
     * @param string $outputPath Path of output image.
     */
    function createScreenshot($url, $width, $height, $outputPath) {

        $top    = 0;
        $left   = 0;

        /** 
        * @see JonnyW\PhantomJs\Http\CaptureRequest
        */
        $request = $this->client->getMessageFactory()->createCaptureRequest($url, 'GET');
        $request->setOutputFile($outputPath);
        $request->setViewportSize($width, $height);
        $request->setCaptureDimensions($width, $height, $top, $left);

        /** 
        * @see JonnyW\PhantomJs\Http\Response 
        */
        $response = $this->client->getMessageFactory()->createResponse();

        // Send the request
        $this->client->send($request, $response);
        //var_export($response);
    }
}