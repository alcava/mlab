<?php
 
use App\Models\User;
 
class SentrySeederSubdomain extends Seeder {
 
    public function run()
    {
        DB::table('users')->delete();
        DB::table('groups')->delete();
        DB::table('users_groups')->delete();
 
        Sentry::getUserProvider()->create(array(
            'email'       => 'alcava@gmail.com',
            'password'    => "selenia",
            'first_name'  => 'Alberto',
            'last_name'   => 'Cavastracci',
            'activated'   => 1,
        ));
 
        Sentry::getGroupProvider()->create(array(
            'name'        => 'Admin',
            'permissions' => array('admin' => 1),
        ));
        Sentry::getGroupProvider()->create(array(
            'name'        => 'Teacher',
            'permissions' => array('teacher' => 1),
        ));
        Sentry::getGroupProvider()->create(array(
            'name'        => 'Student',
            'permissions' => array('student' => 1),
        ));
 
        // Assign user permissions
        $adminUser  = Sentry::getUserProvider()->findByLogin('alcava@gmail.com');
        $adminGroup = Sentry::getGroupProvider()->findByName('Admin');
        $adminUser->addGroup($adminGroup);
    }
 
}