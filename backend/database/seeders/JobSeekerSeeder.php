<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Skill;
use Illuminate\Support\Facades\Hash;

class JobSeekerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobSeekers = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@email.com',
                'password' => Hash::make('password123'),
                'profile' => [
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'phone' => '+1-555-0101',
                    'bio' => 'Passionate frontend developer with 3+ years of experience in React, JavaScript, and modern web technologies. I love creating beautiful, responsive user interfaces and have a strong eye for design.',
                    'user_type' => 'jobseeker',
                    'status' => 'active',
                    'location' => 'New York, NY',
                    'website' => 'https://sarahjohnson.dev',
                    'linkedin' => 'https://linkedin.com/in/sarahjohnson',
                    'github' => 'https://github.com/sarahjohnson',
                    'experience_level' => 'mid',
                    'current_salary' => '75000',
                    'expected_salary' => '85000',
                    'employment_status' => 'employed',
                    'open_to_work' => true,
                    'open_to_relocation' => false,
                    'open_to_remote' => true,
                    'preferred_job_types' => ['full_time', 'contract'],
                    'preferred_locations' => ['New York', 'Remote'],
                    'preferred_industries' => ['Technology', 'E-commerce', 'Fintech']
                ],
                'skills' => ['React', 'JavaScript', 'CSS', 'HTML', 'TypeScript', 'Node.js']
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@email.com',
                'password' => Hash::make('password123'),
                'profile' => [
                    'first_name' => 'Michael',
                    'last_name' => 'Chen',
                    'phone' => '+1-555-0102',
                    'bio' => 'Data analyst with expertise in Python, SQL, and machine learning. I enjoy turning complex data into actionable insights and have experience with various analytics tools.',
                    'user_type' => 'jobseeker',
                    'status' => 'active',
                    'location' => 'San Francisco, CA',
                    'linkedin' => 'https://linkedin.com/in/michaelchen',
                    'github' => 'https://github.com/michaelchen',
                    'experience_level' => 'junior',
                    'current_salary' => '65000',
                    'expected_salary' => '75000',
                    'employment_status' => 'employed',
                    'open_to_work' => true,
                    'open_to_relocation' => true,
                    'open_to_remote' => true,
                    'preferred_job_types' => ['full_time', 'part_time'],
                    'preferred_locations' => ['San Francisco', 'Seattle', 'Remote'],
                    'preferred_industries' => ['Technology', 'Healthcare', 'Finance']
                ],
                'skills' => ['Python', 'SQL', 'Excel', 'Tableau', 'Machine Learning', 'R']
            ],
            [
                'name' => 'Emily Rodriguez',
                'email' => 'emily.rodriguez@email.com',
                'password' => Hash::make('password123'),
                'profile' => [
                    'first_name' => 'Emily',
                    'last_name' => 'Rodriguez',
                    'phone' => '+1-555-0103',
                    'bio' => 'UX/UI Designer with 4+ years of experience creating user-centered designs. I specialize in user research, wireframing, prototyping, and creating intuitive digital experiences.',
                    'user_type' => 'jobseeker',
                    'status' => 'active',
                    'location' => 'Los Angeles, CA',
                    'website' => 'https://emilyrodriguez.design',
                    'linkedin' => 'https://linkedin.com/in/emilyrodriguez',
                    'portfolio' => 'https://dribbble.com/emilyrodriguez',
                    'experience_level' => 'senior',
                    'current_salary' => '90000',
                    'expected_salary' => '105000',
                    'employment_status' => 'employed',
                    'open_to_work' => true,
                    'open_to_relocation' => false,
                    'open_to_remote' => true,
                    'preferred_job_types' => ['full_time', 'contract'],
                    'preferred_locations' => ['Los Angeles', 'Remote'],
                    'preferred_industries' => ['Technology', 'Design', 'E-commerce']
                ],
                'skills' => ['Figma', 'Adobe XD', 'Prototyping', 'User Research', 'Sketch', 'InVision']
            ],
            [
                'name' => 'David Kim',
                'email' => 'david.kim@email.com',
                'password' => Hash::make('password123'),
                'profile' => [
                    'first_name' => 'David',
                    'last_name' => 'Kim',
                    'phone' => '+1-555-0104',
                    'bio' => 'Backend developer specializing in Node.js, Python, and cloud technologies. I have extensive experience building scalable APIs and microservices architecture.',
                    'user_type' => 'jobseeker',
                    'status' => 'active',
                    'location' => 'Seattle, WA',
                    'linkedin' => 'https://linkedin.com/in/davidkim',
                    'github' => 'https://github.com/davidkim',
                    'experience_level' => 'senior',
                    'current_salary' => '110000',
                    'expected_salary' => '125000',
                    'employment_status' => 'employed',
                    'open_to_work' => true,
                    'open_to_relocation' => true,
                    'open_to_remote' => true,
                    'preferred_job_types' => ['full_time'],
                    'preferred_locations' => ['Seattle', 'San Francisco', 'Remote'],
                    'preferred_industries' => ['Technology', 'Cloud Computing', 'Fintech']
                ],
                'skills' => ['Node.js', 'Python', 'MongoDB', 'AWS', 'Docker', 'Kubernetes']
            ],
            [
                'name' => 'Lisa Thompson',
                'email' => 'lisa.thompson@email.com',
                'password' => Hash::make('password123'),
                'profile' => [
                    'first_name' => 'Lisa',
                    'last_name' => 'Thompson',
                    'phone' => '+1-555-0105',
                    'bio' => 'Marketing specialist with expertise in digital marketing, SEO, and social media strategy. I help businesses grow their online presence and drive customer engagement.',
                    'user_type' => 'jobseeker',
                    'status' => 'active',
                    'location' => 'Chicago, IL',
                    'website' => 'https://lisathompson.marketing',
                    'linkedin' => 'https://linkedin.com/in/lisathompson',
                    'experience_level' => 'mid',
                    'current_salary' => '60000',
                    'expected_salary' => '70000',
                    'employment_status' => 'employed',
                    'open_to_work' => true,
                    'open_to_relocation' => false,
                    'open_to_remote' => true,
                    'preferred_job_types' => ['full_time', 'part_time'],
                    'preferred_locations' => ['Chicago', 'Remote'],
                    'preferred_industries' => ['Marketing', 'E-commerce', 'Technology']
                ],
                'skills' => ['Digital Marketing', 'SEO', 'Social Media', 'Analytics', 'Google Ads', 'Content Marketing']
            ],
            [
                'name' => 'James Wilson',
                'email' => 'james.wilson@email.com',
                'password' => Hash::make('password123'),
                'profile' => [
                    'first_name' => 'James',
                    'last_name' => 'Wilson',
                    'phone' => '+1-555-0106',
                    'bio' => 'Product manager with 5+ years of experience leading cross-functional teams and delivering successful products. I specialize in agile methodologies and user-centered product development.',
                    'user_type' => 'jobseeker',
                    'status' => 'active',
                    'location' => 'Boston, MA',
                    'linkedin' => 'https://linkedin.com/in/jameswilson',
                    'experience_level' => 'senior',
                    'current_salary' => '120000',
                    'expected_salary' => '140000',
                    'employment_status' => 'employed',
                    'open_to_work' => true,
                    'open_to_relocation' => true,
                    'open_to_remote' => true,
                    'preferred_job_types' => ['full_time'],
                    'preferred_locations' => ['Boston', 'New York', 'San Francisco', 'Remote'],
                    'preferred_industries' => ['Technology', 'SaaS', 'Fintech']
                ],
                'skills' => ['Product Strategy', 'Agile', 'User Stories', 'Market Research', 'Analytics', 'Leadership']
            ]
        ];

        foreach ($jobSeekers as $jobSeekerData) {
            // Create user
            $user = User::create([
                'name' => $jobSeekerData['name'],
                'email' => $jobSeekerData['email'],
                'password' => $jobSeekerData['password'],
            ]);

            // Create user profile
            $profile = $user->profile()->create($jobSeekerData['profile']);

            // Attach skills
            if (isset($jobSeekerData['skills'])) {
                foreach ($jobSeekerData['skills'] as $skillName) {
                    $skill = Skill::firstOrCreate(
                        ['name' => $skillName],
                        [
                            'slug' => \Illuminate\Support\Str::slug($skillName),
                            'description' => "Skill in {$skillName}",
                            'category' => 'Technical',
                            'is_active' => true
                        ]
                    );
                    $user->skills()->attach($skill->id, [
                        'proficiency_level' => 'intermediate',
                        'years_experience' => rand(1, 5),
                        'is_endorsed' => rand(0, 1),
                        'endorsement_count' => rand(0, 10)
                    ]);
                }
            }
        }
    }
}
