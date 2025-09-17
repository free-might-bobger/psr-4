<?php

namespace App\Observers;
use App\Models\Module;
use App\Repositories\Module\ModuleRepository;

class ModuleObserver {
    public function saved( Module $module ) {
        $request = app()->make( 'request' );
        $scheduleIds = [];

        foreach ( $request->schedules as $schedule ) {
            $scheduleIds[] = $schedule;
        }
        $module->schedules()->sync( $scheduleIds );
        $module->saveQuietly();

        if ( $request->deletedFileIds ) {
            
            foreach ( $request->deletedFileIds as $id ) {
                $image = $module->images()->where( 'id', $id )->delete();
            }
        }

        $moduleRepository = app( ModuleRepository::class );
        $moduleRepository->setModel( $module )->setRequest($request)->filesUpload();
    }

    
}
