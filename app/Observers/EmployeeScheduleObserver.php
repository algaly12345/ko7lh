<?php

namespace App\Observers;

use App\EmployeeSchedule;
use App\Role;

class EmployeeScheduleObserver
{

    public function creating(EmployeeSchedule $employeeSchedule)
    {
        $role = Role::where('name', 'customer')->withoutGlobalScopes()->first();

        if($role != 'customer') {

            if (company()) {

                $employeeSchedule->company_id = company()->id;
            }
        }
    }
    
    /**
     * Handle the employee schedule "created" event.
     *
     * @param  \App\EmployeeSchedule  $employeeSchedule
     * @return void
     */
    public function created(EmployeeSchedule $employeeSchedule)
    {
        //
    }

    /**
     * Handle the employee schedule "updated" event.
     *
     * @param  \App\EmployeeSchedule  $employeeSchedule
     * @return void
     */
    public function updated(EmployeeSchedule $employeeSchedule)
    {
        //
    }

    /**
     * Handle the employee schedule "deleted" event.
     *
     * @param  \App\EmployeeSchedule  $employeeSchedule
     * @return void
     */
    public function deleted(EmployeeSchedule $employeeSchedule)
    {
        //
    }

    /**
     * Handle the employee schedule "restored" event.
     *
     * @param  \App\EmployeeSchedule  $employeeSchedule
     * @return void
     */
    public function restored(EmployeeSchedule $employeeSchedule)
    {
        //
    }

    /**
     * Handle the employee schedule "force deleted" event.
     *
     * @param  \App\EmployeeSchedule  $employeeSchedule
     * @return void
     */
    public function forceDeleted(EmployeeSchedule $employeeSchedule)
    {
        //
    }
}
