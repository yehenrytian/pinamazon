<?php
/**
 * For a running Search Demo see: http://amazonecs.pixel-web.org
 */

if (is_file('sampleSettings.php'))
{
  include 'sampleSettings.php';
}

defined('AWS_API_KEY') or define('AWS_API_KEY', 'API KEY');
defined('AWS_API_SECRET_KEY') or define('AWS_API_SECRET_KEY', 'SECRET KEY');
defined('AWS_ASSOCIATE_TAG') or define('AWS_ASSOCIATE_TAG', 'ASSOCIATE TAG');

require_once 'AmazonECS.class.php';


?>
