<?php

namespace Modules\Task\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaskDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['title' => 'Backlog', 'order' => 1],         // Tasks that are planned but not started
            ['title' => 'To Do', 'order' => 2],           // Tasks ready to be worked on
            ['title' => 'In Progress', 'order' => 3],     // Tasks currently being worked on
            ['title' => 'Review', 'order' => 4],          // Tasks waiting for approval/review
            ['title' => 'Completed', 'order' => 5],       // Successfully finished tasks
            ['title' => 'Archived', 'order' => 6],        // Closed tasks (kept for reference)
        ];        

        foreach ($statuses as $status) {
            DB::table('statuses')->insert([
                'title' => $status['title'],
                'slug' =>  Str::slug($status['title']),
                'order' => $status['order'],
                'user_id' => 1,
            ]);
        }

        $tasks = [
            // Backlog (Status ID: 1)
            ['title' => 'Research New Feature', 'description' => 'Analyze competitor features.', 'order' => 1, 'status_id' => 1],
            ['title' => 'Draft Initial UI', 'description' => 'Sketch wireframes for new page.', 'order' => 2, 'status_id' => 1],
            ['title' => 'Create API Docs', 'description' => 'Document all API endpoints.', 'order' => 3, 'status_id' => 1],
        
            // To Do (Status ID: 2)
            ['title' => 'Set Up Database', 'description' => 'Create tables and relationships.', 'order' => 1, 'status_id' => 2],
            ['title' => 'Implement Authentication', 'description' => 'Set up JWT authentication.', 'order' => 2, 'status_id' => 2],
            ['title' => 'Configure CI/CD', 'description' => 'Set up GitHub Actions.', 'order' => 3, 'status_id' => 2],
        
            // In Progress (Status ID: 3)
            ['title' => 'Develop Login Page', 'description' => 'Build and style login UI.', 'order' => 1, 'status_id' => 3],
            ['title' => 'Optimize API Calls', 'description' => 'Reduce API response time.', 'order' => 2, 'status_id' => 3],
            ['title' => 'Fix Payment Gateway', 'description' => 'Resolve PayPal integration issues.', 'order' => 3, 'status_id' => 3],
        
            // Review (Status ID: 4)
            ['title' => 'Code Review for API', 'description' => 'Check security best practices.', 'order' => 1, 'status_id' => 4],
            ['title' => 'UX Testing', 'description' => 'Collect user feedback on UI.', 'order' => 2, 'status_id' => 4],
        
            // Completed (Status ID: 5)
            ['title' => 'Deploy Staging Server', 'description' => 'Push latest version to staging.', 'order' => 1, 'status_id' => 5],
            ['title' => 'Update Documentation', 'description' => 'Add missing API endpoints.', 'order' => 2, 'status_id' => 5],
        
            // Archived (Status ID: 6)
            ['title' => 'Legacy Code Cleanup', 'description' => 'Refactor old unused code.', 'order' => 1, 'status_id' => 6],
        ];        

        foreach ($tasks as $task) {
            DB::table('tasks')->insert([
                'title' => $task['title'],
                'description' => $task['description'],
                'order' => $task['order'],
                'user_id' => 1,
                'status_id' => $task['status_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
