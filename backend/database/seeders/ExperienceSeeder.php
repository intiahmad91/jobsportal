<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Experience;

class ExperienceSeeder extends Seeder
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

        $experiences = [
            [
                'title' => 'Senior Frontend Developer',
                'company' => 'TechCorp Solutions',
                'location' => 'New York, NY',
                'start_date' => '2022-01-15',
                'end_date' => null,
                'is_current' => true,
                'description' => 'Led frontend development team of 5 developers. Built responsive web applications using React, TypeScript, and modern CSS frameworks. Implemented CI/CD pipelines and improved application performance by 40%.'
            ],
            [
                'title' => 'Frontend Developer',
                'company' => 'Digital Innovations Inc',
                'location' => 'San Francisco, CA',
                'start_date' => '2020-06-01',
                'end_date' => '2021-12-31',
                'is_current' => false,
                'description' => 'Developed and maintained multiple client projects using React, Vue.js, and Angular. Collaborated with UX/UI designers to implement pixel-perfect designs. Reduced page load times by 30% through optimization techniques.'
            ],
            [
                'title' => 'Junior Web Developer',
                'company' => 'StartupXYZ',
                'location' => 'Austin, TX',
                'start_date' => '2019-03-01',
                'end_date' => '2020-05-31',
                'is_current' => false,
                'description' => 'Built responsive websites using HTML, CSS, JavaScript, and jQuery. Worked closely with senior developers to learn best practices and modern development workflows.'
            ],
            [
                'title' => 'Data Analyst',
                'company' => 'Analytics Pro',
                'location' => 'Chicago, IL',
                'start_date' => '2021-08-01',
                'end_date' => null,
                'is_current' => true,
                'description' => 'Analyze large datasets using Python, SQL, and Tableau. Create data visualizations and reports for stakeholders. Implemented automated reporting system that reduced manual work by 60%.'
            ],
            [
                'title' => 'Junior Data Analyst',
                'company' => 'Data Insights Co',
                'location' => 'Seattle, WA',
                'start_date' => '2020-01-15',
                'end_date' => '2021-07-31',
                'is_current' => false,
                'description' => 'Performed data cleaning and analysis using Excel, SQL, and Python. Created dashboards and reports for business intelligence. Collaborated with cross-functional teams to identify data-driven insights.'
            ],
            [
                'title' => 'UX/UI Designer',
                'company' => 'Design Studio',
                'location' => 'Los Angeles, CA',
                'start_date' => '2020-09-01',
                'end_date' => null,
                'is_current' => true,
                'description' => 'Lead UX/UI design for mobile and web applications. Conduct user research, create wireframes, prototypes, and high-fidelity designs. Improved user engagement by 45% through design optimization.'
            ],
            [
                'title' => 'Product Designer',
                'company' => 'Creative Agency',
                'location' => 'Miami, FL',
                'start_date' => '2019-06-01',
                'end_date' => '2020-08-31',
                'is_current' => false,
                'description' => 'Designed user interfaces for various client projects. Created design systems and style guides. Collaborated with developers to ensure design implementation accuracy.'
            ],
            [
                'title' => 'Backend Developer',
                'company' => 'CloudTech Solutions',
                'location' => 'Denver, CO',
                'start_date' => '2021-03-01',
                'end_date' => null,
                'is_current' => true,
                'description' => 'Develop and maintain scalable backend services using Node.js, Python, and microservices architecture. Implemented API integrations and database optimizations. Reduced API response time by 50%.'
            ],
            [
                'title' => 'Full Stack Developer',
                'company' => 'WebDev Agency',
                'location' => 'Portland, OR',
                'start_date' => '2019-09-01',
                'end_date' => '2021-02-28',
                'is_current' => false,
                'description' => 'Developed full-stack web applications using React, Node.js, and MongoDB. Implemented user authentication, payment processing, and real-time features. Led a team of 3 junior developers.'
            ],
            [
                'title' => 'Digital Marketing Manager',
                'company' => 'Marketing Pro',
                'location' => 'Phoenix, AZ',
                'start_date' => '2020-11-01',
                'end_date' => null,
                'is_current' => true,
                'description' => 'Develop and execute digital marketing strategies across multiple channels. Manage SEO, SEM, social media, and content marketing campaigns. Increased organic traffic by 80% and conversion rates by 35%.'
            ],
            [
                'title' => 'Marketing Specialist',
                'company' => 'Growth Marketing Co',
                'location' => 'Dallas, TX',
                'start_date' => '2019-04-01',
                'end_date' => '2020-10-31',
                'is_current' => false,
                'description' => 'Executed digital marketing campaigns for B2B and B2C clients. Managed Google Ads, Facebook Ads, and email marketing campaigns. Achieved 25% increase in lead generation and 40% improvement in ROI.'
            ],
            [
                'title' => 'Product Manager',
                'company' => 'Innovation Labs',
                'location' => 'Boston, MA',
                'start_date' => '2020-07-01',
                'end_date' => null,
                'is_current' => true,
                'description' => 'Lead product strategy and roadmap for SaaS platform with 50K+ users. Manage cross-functional teams of 12+ members. Implemented agile methodologies and improved product delivery by 40%.'
            ],
            [
                'title' => 'Senior Product Manager',
                'company' => 'TechStart Inc',
                'location' => 'San Diego, CA',
                'start_date' => '2018-05-01',
                'end_date' => '2020-06-30',
                'is_current' => false,
                'description' => 'Led product development for mobile applications. Conducted market research, user interviews, and competitive analysis. Launched 3 successful products with combined 100K+ downloads.'
            ]
        ];

        foreach ($jobSeekers as $index => $jobSeeker) {
            // Assign 1-3 experiences per job seeker
            $numExperiences = rand(1, 3);
            $selectedExperiences = array_slice($experiences, $index * 2, $numExperiences);
            
            foreach ($selectedExperiences as $expData) {
                Experience::create([
                    'user_id' => $jobSeeker->id,
                    'title' => $expData['title'],
                    'company' => $expData['company'],
                    'location' => $expData['location'],
                    'start_date' => $expData['start_date'],
                    'end_date' => $expData['end_date'],
                    'is_current' => $expData['is_current'],
                    'description' => $expData['description']
                ]);
            }
        }

        $this->command->info('Experiences seeded successfully!');
    }
}
