<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/12/2015
 * Time: 2:08 PM
 */
class GetRoomListCommand extends TServiceCommand
{

    protected function run()
    {
        $list = TEvent::getRoomAndResourceList();
        $this->SetReturnValue($list);
    }
}