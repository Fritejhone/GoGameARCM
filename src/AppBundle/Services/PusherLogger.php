<?php
/**
 * Created by PhpStorm.
 * User: S059184
 * Date: 27/03/2018
 * Time: 13:42
 */

namespace AppBundle\Services;


class PusherLogger
{
    public function log( $msg ) {
        dump( $msg . "\n" );
    }

}