<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Education;

class EducationSeeder extends Seeder
{
    public function run()
    {
        // Get job seeker users
        $jobSeekers = User::whereHas('profile', function($query) {
            $query->where('user_type', 'jobseeker');
        })->get();

        if ($jobSeekers->isEmpty()) {
            $this->command->info('No job seekers found. Please run JobSeekerSeeder first.');
            return;
        }

        $educations = [
            [
                'degree' => 'Master of Science',
                'field_of_study' => 'Computer Science',
                'institution' => 'Stanford University',
                'location' => 'Stanford, CA',
                'start_date' => '2018-09-01',
                'end_date' => '2020-06-30',
                'gpa' => '3.8',
                'description' => 'Specialized in Machine Learning and Artificial Intelligence. Completed thesis on "Deep Learning Applications in Natural Language Processing".'
            ],
            [
                'degree' => 'Bachelor of Science',
                'field_of_study' => 'Computer Science',
                'institution' => 'University of California, Berkeley',
                'location' => 'Berkeley, CA',
                'start_date' => '2014-09-01',
                'end_date' => '2018-05-31',
                'gpa' => '3.6',
                'description' => 'Focus on Software Engineering and Data Structures. Active member of Computer Science Society and Hackathon team.'
            ],
            [
                'degree' => 'Master of Business Administration',
                'field_of_study' => 'Digital Marketing',
                'institution' => 'New York University',
                'location' => 'New York, NY',
                'start_date' => '2019-09-01',
                'end_date' => '2021-05-31',
                'gpa' => '3.7',
                'description' => 'Specialized in Digital Marketing Strategy and Analytics. Completed capstone project on "Social Media Marketing ROI Optimization".'
            ],
            [
                'degree' => 'Bachelor of Arts',
                'field_of_study' => 'Graphic Design',
                'institution' => 'Art Center College of Design',
                'location' => 'Pasadena, CA',
                'start_date' => '2016-09-01',
                'end_date' => '2020-05-31',
                'gpa' => '3.9',
                'description' => 'Focus on User Experience Design and Visual Communication. Portfolio featured in annual student exhibition.'
            ],
            [
                'degree' => 'Master of Science',
                'field_of_study' => 'Data Science',
                'institution' => 'Carnegie Mellon University',
                'location' => 'Pittsburgh, PA',
                'start_date' => '2019-08-01',
                'end_date' => '2021-05-31',
                'gpa' => '3.8',
                'description' => 'Specialized in Machine Learning and Statistical Analysis. Research project on "Predictive Analytics for Business Intelligence".'
            ],
            [
                'degree' => 'Bachelor of Science',
                'field_of_study' => 'Information Technology',
                'institution' => 'Georgia Institute of Technology',
                'location' => 'Atlanta, GA',
                'start_date' => '2015-08-01',
                'end_date' => '2019-05-31',
                'gpa' => '3.5',
                'description' => 'Focus on Software Development and Database Management. President of IT Student Association.'
            ],
            [
                'degree' => 'Master of Science',
                'field_of_study' => 'Product Management',
                'institution' => 'Northwestern University',
                'location' => 'Evanston, IL',
                'start_date' => '2018-09-01',
                'end_date' => '2020-06-30',
                'gpa' => '3.7',
                'description' => 'Specialized in Product Strategy and User Research. Capstone project on "Agile Product Development Methodologies".'
            ],
            [
                'degree' => 'Bachelor of Science',
                'field_of_study' => 'Business Administration',
                'institution' => 'University of Pennsylvania',
                'location' => 'Philadelphia, PA',
                'start_date' => '2014-09-01',
                'end_date' => '2018-05-31',
                'gpa' => '3.6',
                'description' => 'Concentration in Marketing and Entrepreneurship. Founded student startup that won university business plan competition.'
            ],
            [
                'degree' => 'Associate Degree',
                'field_of_study' => 'Web Development',
                'institution' => 'Community College of Denver',
                'location' => 'Denver, CO',
                'start_date' => '2017-01-15',
                'end_date' => '2019-05-31',
                'gpa' => '3.8',
                'description' => 'Focus on Full-Stack Web Development. Completed internship program with local tech companies.'
            ],
            [
                'degree' => 'Certificate',
                'field_of_study' => 'Digital Marketing',
                'institution' => 'Google Digital Academy',
                'location' => 'Online',
                'start_date' => '2020-01-01',
                'end_date' => '2020-06-30',
                'gpa' => null,
                'description' => 'Google Analytics and Google Ads certified. Specialized in Search Engine Optimization and Pay-Per-Click advertising.'
            ],
            [
                'degree' => 'Bachelor of Science',
                'field_of_study' => 'Computer Engineering',
                'institution' => 'University of Washington',
                'location' => 'Seattle, WA',
                'start_date' => '2015-09-01',
                'end_date' => '2019-06-30',
                'gpa' => '3.4',
                'description' => 'Focus on Software Engineering and System Design. Active in robotics club and programming competitions.'
            ],
            [
                'degree' => 'Master of Fine Arts',
                'field_of_study' => 'Interactive Design',
                'institution' => 'Rhode Island School of Design',
                'location' => 'Providence, RI',
                'start_date' => '2018-09-01',
                'end_date' => '2020-05-31',
                'gpa' => '3.9',
                'description' => 'Specialized in User Experience Design and Human-Computer Interaction. Thesis project on "Accessibility in Digital Design".'
            ]
        ];

        foreach ($jobSeekers as $index => $jobSeeker) {
            // Assign 1-2 educations per job seeker
            $numEducations = rand(1, 2);
            $selectedEducations = array_slice($educations, $index * 2, $numEducations);
            
            foreach ($selectedEducations as $eduData) {
                Education::create([
                    'user_id' => $jobSeeker->id,
                    'degree' => $eduData['degree'],
                    'field_of_study' => $eduData['field_of_study'],
                    'institution' => $eduData['institution'],
                    'location' => $eduData['location'],
                    'start_date' => $eduData['start_date'],
                    'end_date' => $eduData['end_date'],
                    'grade' => $eduData['gpa'],
                    'description' => $eduData['description']
                ]);
            }
        }

        $this->command->info('Educations seeded successfully!');
    }
}
