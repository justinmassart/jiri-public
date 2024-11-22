<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Contact;
use App\Models\ContactImage;
use App\Models\Jiri;
use App\Models\JiriProject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $didier = User::factory()->create([
            'firstname' => 'Didier',
            'lastname' => 'reidid',
            'email' => 'didier@gmail.com',
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $random_number_jiris = random_int(2, 5);
            $jiris = Jiri::factory($random_number_jiris)->create([
                'user_id' => $user->id,
            ]);

            foreach ($jiris as $jiri) {
                $random_number_projects = random_int(1, 3);

                $jiri_projects = JiriProject::factory($random_number_projects)->create([
                    'jiri_id' => $jiri->id,
                ]);

                $random_number_students = random_int(4, 12);
                $students = Contact::factory()->count($random_number_students)->create([
                    'user_id' => $user->id,
                ]);

                $random_number_evaluators = random_int(3, 10);
                $evaluators = Contact::factory()->count($random_number_evaluators)->create([
                    'user_id' => $user->id,
                ]);

                $contacts = $students->concat($evaluators);

                foreach ($contacts as $contact) {
                    ContactImage::factory()->create([
                        'contact_id' => $contact->id,
                    ]);

                    $attendanceRole = in_array($contact, $students->all()) ? 'student' : 'evaluator';

                    if ($attendanceRole === 'student') {
                        $project = Project::factory()->create([
                            'urls' => json_encode([
                                ['link' => 'http://github.com', 'type' => 'github'],
                                ['link' => 'http://design.com', 'type' => 'design'],
                                ['link' => 'http://site.com', 'type' => 'site'],
                            ]),
                            'contact_id' => $contact->id,
                            'jiri_project_id' => $jiri_projects->random()->id,
                        ]);

                        Attendance::factory()->create([
                            'contact_id' => $contact->id,
                            'jiri_id' => $jiri->id,
                            'role' => 'student',
                        ]);
                    } else {
                        Attendance::factory()->create([
                            'contact_id' => $contact->id,
                            'jiri_id' => $jiri->id,
                            'role' => 'evaluator',
                        ]);
                    }
                }
            }
        }

        User::factory()->create([
            'firstname' => 'arthur',
            'lastname' => 'morgan',
            'email' => 'arthurmorgan@gmail.com',
        ]);
    }
}
