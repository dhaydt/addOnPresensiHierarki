<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Department first
        $department = \App\Models\Departement::create([
            'DeptID' => 1,
            'DeptName' => 'IT Department',
        ]);

        // Create test users with proper structure
        $admin = \App\Models\User::create([
            'userid' => 1001,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'defaultdeptid' => 1,
        ]);

        // Create employees
        $manager1 = \App\Models\User::create([
            'userid' => 1002,
            'name' => 'John Manager',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'defaultdeptid' => 1,
        ]);

        $manager2 = \App\Models\User::create([
            'userid' => 1003,
            'name' => 'Jane Manager',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'defaultdeptid' => 1,
        ]);

        $employee1 = \App\Models\User::create([
            'userid' => 1004,
            'name' => 'Bob Employee',
            'email' => 'bob@example.com',
            'password' => bcrypt('password'),
            'defaultdeptid' => 1,
        ]);

        $employee2 = \App\Models\User::create([
            'userid' => 1005,
            'name' => 'Alice Employee',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
            'defaultdeptid' => 1,
        ]);

        $employee3 = \App\Models\User::create([
            'userid' => 1006,
            'name' => 'Charlie Employee',
            'email' => 'charlie@example.com',
            'password' => bcrypt('password'),
            'defaultdeptid' => 1,
        ]);

        // Create hierarchy relationships
        \App\Models\EmployeeSuperior::create([
            'employee_id' => $manager1->userid,
            'superior_id' => $admin->userid,
        ]);

        \App\Models\EmployeeSuperior::create([
            'employee_id' => $manager2->userid,
            'superior_id' => $admin->userid,
        ]);

        \App\Models\EmployeeSuperior::create([
            'employee_id' => $employee1->userid,
            'superior_id' => $manager1->userid,
        ]);

        \App\Models\EmployeeSuperior::create([
            'employee_id' => $employee2->userid,
            'superior_id' => $manager1->userid,
        ]);

        \App\Models\EmployeeSuperior::create([
            'employee_id' => $employee3->userid,
            'superior_id' => $manager2->userid,
        ]);
    }
}
