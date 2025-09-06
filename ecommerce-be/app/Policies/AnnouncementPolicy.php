<?php

namespace App\Policies;

use App\Model\User;
use App\Model\Announcement;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Traits\Obfuscate\OptimusPolicy;
class AnnouncementPolicy
{
    use HandlesAuthorization, OptimusPolicy ;
    
    public function index(User $user)
    {
        return $this->accessable('Announcements');

    }

    /**
     * Determine whether the user can view any announcements.
     *
     * @param  \App\Model\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        
    }

    /**
     * Determine whether the user can view the announcement.
     *
     * @param  \App\Model\User  $user
     * @param  \App\Model\Announcement  $announcement
     * @return mixed
     */
    public function view(User $user, Announcement $announcement)
    {
        //
    }

    /**
     * Determine whether the user can create announcements.
     *
     * @param  \App\Model\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the announcement.
     *
     * @param  \App\Model\User  $user
     * @param  \App\Model\Announcement  $announcement
     * @return mixed
     */
    public function update(User $user, Announcement $announcement)
    {
        //
    }

    /**
     * Determine whether the user can delete the announcement.
     *
     * @param  \App\Model\User  $user
     * @param  \App\Model\Announcement  $announcement
     * @return mixed
     */
    public function delete(User $user, Announcement $announcement)
    {
        //
    }

    /**
     * Determine whether the user can restore the announcement.
     *
     * @param  \App\Model\User  $user
     * @param  \App\Model\Announcement  $announcement
     * @return mixed
     */
    public function restore(User $user, Announcement $announcement)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the announcement.
     *
     * @param  \App\Model\User  $user
     * @param  \App\Model\Announcement  $announcement
     * @return mixed
     */
    public function forceDelete(User $user, Announcement $announcement)
    {
        //
    }
}
