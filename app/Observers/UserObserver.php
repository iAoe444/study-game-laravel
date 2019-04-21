<?php

namespace App\Observers;

use App\User;

class UserObserver
{
    /**
     * 监听创建用户事件.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(User $user)
    {
        // var_dump("hello");
    }

    /**
     * 监听删除用户事件.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        //
    }
}