<?php

namespace App\Contracts\Queue;

/**
 * Implement this interface if you do not want the job handle to be executed after the reset has been done.
 */
interface ShouldNotHandleAfterBuildingReset
{

}