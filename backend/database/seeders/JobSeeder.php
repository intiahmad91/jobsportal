<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobLocation;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get companies, categories, and locations
        $companies = Company::all();
        $categories = JobCategory::all();
        $locations = JobLocation::all();
        
        if ($companies->isEmpty() || $categories->isEmpty() || $locations->isEmpty()) {
            $this->command->warn('Companies, categories, or locations not found. Please run CompanySeeder, JobCategorySeeder, and JobLocationSeeder first.');
            return;
        }

        // Helper function to get location ID by city
        $getLocationId = function($city) use ($locations) {
            $location = $locations->where('city', $city)->first();
            return $location ? $location->id : $locations->first()->id; // fallback to first location
        };

        $jobs = [
            // Technology Jobs
            [
                'title' => 'Senior UI/UX Designer',
                'description' => 'We are looking for an experienced UI/UX Designer to join our team. You will be responsible for creating beautiful and intuitive user interfaces for our web and mobile applications.',
                'company_id' => $companies->where('name', 'Amazon')->first() ? $companies->where('name', 'Amazon')->first()->id : $companies->first()->id,
                'category_id' => $categories->where('name', 'Design & Creative')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'senior',
                'location_id' => $getLocationId('California'),
                'min_salary' => '250',
                'max_salary' => '250',
                'salary_currency' => '$',
                'salary_period' => 'hourly',
                'status' => 'active',
                'benefits' => 'Health insurance, Dental insurance, Vision insurance, 401(k) matching, Flexible work hours, Remote work options',
                'requirements' => 'Bachelor degree in Design or related field, 5+ years of UI/UX design experience, Proficiency in Figma, Adobe Creative Suite, and prototyping tools',
                'remote_work' => true,
                'positions_available' => 1,
                'is_featured' => true,
                'tags' => ['UI / UX Design', 'Web Developer', 'SEO', 'Web Design']
            ],
            [
                'title' => 'Frontend Developer',
                'description' => 'Join our frontend team to build beautiful and responsive user interfaces. You will work with modern JavaScript frameworks, collaborate with designers, and ensure optimal user experience across all devices.',
                'company_id' => $companies->where('name', 'TechCorp Solutions')->first()->id,
                'category_id' => $categories->where('name', 'Information Technology')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location_id' => $getLocationId('Austin'),
                'min_salary' => '80000',
                'max_salary' => '120000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Dental insurance, 401(k) matching, Flexible work hours, Learning budget',
                'requirements' => 'Bachelor degree in Computer Science or related field, 3+ years of frontend development experience, Proficiency in React, Vue.js, or Angular, Experience with CSS frameworks and responsive design',
                'remote_work' => false,
                'positions_available' => 1,
                'tags' => ['React', 'Vue.js', 'CSS', 'JavaScript', 'Frontend']
            ],
            [
                'title' => 'Backend Developer',
                'description' => 'We need a skilled backend developer to build robust APIs and server-side applications. You will work with microservices architecture, databases, and cloud infrastructure to deliver scalable solutions.',
                'company_id' => $companies->where('name', 'TechCorp Solutions')->first()->id,
                'category_id' => $categories->where('name', 'Information Technology')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location_id' => $getLocationId('Seattle'),
                'min_salary' => '90000',
                'max_salary' => '130000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Dental insurance, Vision insurance, 401(k) matching, Remote work options, Stock options',
                'requirements' => 'Bachelor degree in Computer Science or related field, 3+ years of backend development experience, Proficiency in Python, Java, or Node.js, Experience with databases (PostgreSQL, MongoDB), Knowledge of RESTful APIs and microservices',
                'remote_work' => true,
                'positions_available' => 1,
                'tags' => ['Python', 'Java', 'Node.js', 'PostgreSQL', 'Microservices']
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'We are looking for a DevOps Engineer to help us build and maintain our cloud infrastructure and deployment pipelines. You will work with containerization, CI/CD, and monitoring systems.',
                'company_id' => $companies->where('name', 'FullTimeZ')->first()->id,
                'category_id' => $categories->where('name', 'Information Technology')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'senior',
                'location_id' => $getLocationId('New York'), // Remote jobs mapped to a default location
                'min_salary' => '110000',
                'max_salary' => '160000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, 401(k) matching, Remote work options, Professional development budget, Flexible PTO, Home office stipend',
                'requirements' => 'Bachelor degree in Computer Science or related field, 4+ years of DevOps experience, Experience with AWS, Docker, Kubernetes, and CI/CD pipelines, Knowledge of infrastructure as code (Terraform/CloudFormation)',
                'remote_work' => true,
                'positions_available' => 1,
                'is_featured' => true,
                'tags' => ['AWS', 'Docker', 'Kubernetes', 'CI/CD', 'Terraform']
            ],
            [
                'title' => 'Data Scientist',
                'description' => 'We are seeking a talented Data Scientist to analyze complex data sets and provide insights to drive business decisions. You will work with machine learning models, statistical analysis, and data visualization.',
                'company_id' => $companies->where('name', 'Global Finance Inc')->first()->id,
                'category_id' => $categories->where('name', 'Information Technology')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'senior',
                'location_id' => $getLocationId('New York'),
                'min_salary' => '130000',
                'max_salary' => '180000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Stock options, Flexible work schedule, Learning and development budget, Gym membership',
                'requirements' => 'Master degree in Data Science, Statistics, or related field, 4+ years of experience in data analysis, Proficiency in Python, R, and machine learning frameworks, Experience with SQL and big data tools',
                'remote_work' => false,
                'positions_available' => 1,
                'tags' => ['Python', 'R', 'Machine Learning', 'SQL', 'Statistics']
            ],

            // Marketing Jobs
            [
                'title' => 'Marketing Manager',
                'description' => 'Join our marketing team as a Marketing Manager. You will be responsible for developing and implementing marketing strategies to promote our products and services, managing campaigns, and analyzing performance metrics.',
                'company_id' => $companies->where('name', 'Creative Design Studio')->first()->id,
                'category_id' => $categories->where('name', 'Marketing & Sales')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location_id' => $getLocationId('Los Angeles'),
                'min_salary' => '70000',
                'max_salary' => '95000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Paid time off, Professional development opportunities, Team building events, Creative workspace',
                'requirements' => 'Bachelor degree in Marketing or related field, 3+ years of marketing experience, Strong communication and analytical skills, Experience with digital marketing tools and platforms',
                'remote_work' => false,
                'positions_available' => 1,
                'tags' => ['Digital Marketing', 'Campaign Management', 'Analytics', 'Social Media', 'Content Marketing']
            ],
            [
                'title' => 'Digital Marketing Specialist',
                'description' => 'We are looking for a Digital Marketing Specialist to manage our online presence and digital campaigns. You will work with social media, email marketing, SEO, and paid advertising.',
                'company_id' => $companies->where('name', 'Creative Design Studio')->first()->id,
                'category_id' => $categories->where('name', 'Marketing & Sales')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'junior',
                'location_id' => $getLocationId('Chicago'),
                'min_salary' => '50000',
                'max_salary' => '70000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Paid time off, Professional development opportunities, Flexible work hours',
                'requirements' => 'Bachelor degree in Marketing or related field, 1-2 years of digital marketing experience, Knowledge of social media platforms, SEO, and Google Analytics',
                'remote_work' => false,
                'positions_available' => 2,
                'tags' => ['Social Media', 'SEO', 'Google Analytics', 'Email Marketing', 'PPC']
            ],

            // Design Jobs
            [
                'title' => 'UX/UI Designer',
                'description' => 'Join our design team as a UX/UI Designer. You will create user-centered designs for our digital products and ensure excellent user experience. You will work closely with product managers and developers.',
                'company_id' => $companies->where('name', 'Creative Design Studio')->first()->id,
                'category_id' => $categories->where('name', 'Design & Creative')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location_id' => $getLocationId('Portland'),
                'min_salary' => '75000',
                'max_salary' => '105000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Dental insurance, Vision insurance, Flexible work hours, Creative workspace, Design tools budget',
                'requirements' => 'Bachelor degree in Design or related field, 3+ years of UX/UI design experience, Proficiency in Figma, Adobe Creative Suite, and prototyping tools, Strong portfolio',
                'remote_work' => false,
                'positions_available' => 1,
                'tags' => ['Figma', 'Adobe Creative Suite', 'Prototyping', 'User Research', 'UI/UX']
            ],
            [
                'title' => 'Graphic Designer',
                'description' => 'We need a creative Graphic Designer to produce visual content for our marketing campaigns and brand materials. You will work on logos, brochures, social media graphics, and other marketing materials.',
                'company_id' => $companies->where('name', 'Creative Design Studio')->first()->id,
                'category_id' => $categories->where('name', 'Design & Creative')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'junior',
                'location_id' => $getLocationId('Miami'),
                'min_salary' => '45000',
                'max_salary' => '65000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Paid time off, Creative workspace, Design tools budget, Team events',
                'requirements' => 'Bachelor degree in Graphic Design or related field, 1-2 years of design experience, Proficiency in Adobe Creative Suite, Strong creative and communication skills',
                'remote_work' => false,
                'positions_available' => 1,
                'tags' => ['Adobe Creative Suite', 'Illustrator', 'Photoshop', 'Brand Design', 'Print Design']
            ],

            // Healthcare Jobs
            [
                'title' => 'Registered Nurse',
                'description' => 'We are seeking a compassionate Registered Nurse to provide high-quality patient care in our healthcare facility. You will work with a multidisciplinary team to ensure patient safety and comfort.',
                'company_id' => $companies->where('name', 'Healthcare Plus')->first()->id,
                'category_id' => $categories->where('name', 'Healthcare')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location_id' => $getLocationId('Boston'),
                'min_salary' => '65000',
                'max_salary' => '85000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Dental insurance, Vision insurance, 401(k) matching, Paid time off, Continuing education support',
                'requirements' => 'Bachelor of Science in Nursing (BSN), Valid RN license, 2+ years of nursing experience, BLS and ACLS certifications, Strong communication and critical thinking skills',
                'remote_work' => false,
                'positions_available' => 3,
                'tags' => ['Nursing', 'Patient Care', 'Healthcare', 'Medical', 'BSN']
            ],
            [
                'title' => 'Physical Therapist',
                'description' => 'Join our rehabilitation team as a Physical Therapist. You will help patients recover from injuries and improve their mobility through therapeutic exercises and treatments.',
                'company_id' => $companies->where('name', 'Healthcare Plus')->first()->id,
                'category_id' => $categories->where('name', 'Healthcare')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location_id' => $getLocationId('Denver'),
                'min_salary' => '70000',
                'max_salary' => '95000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Dental insurance, Vision insurance, 401(k) matching, Paid time off, Professional development opportunities',
                'requirements' => 'Doctor of Physical Therapy (DPT) degree, Valid PT license, 2+ years of clinical experience, Strong interpersonal and communication skills',
                'remote_work' => false,
                'positions_available' => 2,
                'tags' => ['Physical Therapy', 'Rehabilitation', 'DPT', 'Patient Care', 'Healthcare']
            ],

            // Finance Jobs
            [
                'title' => 'Financial Analyst',
                'description' => 'We are looking for a Financial Analyst to analyze financial data and provide insights to support business decisions. You will work with budgets, forecasts, and financial reporting.',
                'company_id' => $companies->where('name', 'Global Finance Inc')->first()->id,
                'category_id' => $categories->where('name', 'Finance & Accounting')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location_id' => $getLocationId('New York'),
                'min_salary' => '75000',
                'max_salary' => '100000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Dental insurance, Vision insurance, 401(k) matching, Bonus potential, Professional development',
                'requirements' => 'Bachelor degree in Finance, Accounting, or related field, 2+ years of financial analysis experience, Proficiency in Excel and financial modeling, Strong analytical skills',
                'remote_work' => false,
                'positions_available' => 1,
                'tags' => ['Financial Analysis', 'Excel', 'Financial Modeling', 'Budgeting', 'Forecasting']
            ],

            // Sales Jobs
            [
                'title' => 'Sales Representative',
                'description' => 'Join our sales team as a Sales Representative. You will be responsible for building relationships with clients, identifying sales opportunities, and achieving sales targets.',
                'company_id' => $companies->where('name', 'TechCorp Solutions')->first()->id,
                'category_id' => $categories->where('name', 'Marketing & Sales')->first()->id,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'junior',
                'location_id' => $getLocationId('Phoenix'),
                'min_salary' => '45000',
                'max_salary' => '70000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Commission structure, Paid time off, Sales training, Car allowance',
                'requirements' => 'Bachelor degree in Business or related field, 1+ years of sales experience, Strong communication and negotiation skills, Self-motivated and results-oriented',
                'remote_work' => false,
                'positions_available' => 2,
                'tags' => ['Sales', 'Client Relations', 'Negotiation', 'CRM', 'Business Development']
            ],

            // Part-time and Contract Jobs
            [
                'title' => 'Content Writer (Part-time)',
                'description' => 'We are looking for a creative Content Writer to create engaging content for our blog, social media, and marketing materials. This is a part-time position with flexible hours.',
                'company_id' => $companies->where('name', 'Creative Design Studio')->first()->id,
                'category_id' => $categories->where('name', 'Marketing & Sales')->first()->id,
                'user_id' => 1,
                'employment_type' => 'part_time',
                'experience_level' => 'junior',
                'location_id' => $getLocationId('New York'), // Remote jobs mapped to a default location
                'min_salary' => '25',
                'max_salary' => '35',
                'salary_currency' => 'USD',
                'salary_period' => 'hourly',
                'status' => 'active',
                'benefits' => 'Flexible work hours, Remote work, Professional development opportunities',
                'requirements' => 'Bachelor degree in English, Journalism, or related field, 1+ years of writing experience, Strong writing and editing skills, Knowledge of SEO principles',
                'remote_work' => true,
                'positions_available' => 1,
                'tags' => ['Content Writing', 'Blog Writing', 'SEO', 'Social Media', 'Copywriting']
            ],
            [
                'title' => 'Software Developer (Contract)',
                'description' => 'We need a Software Developer for a 6-month contract to help with a specific project. You will work on developing new features and fixing bugs in our existing application.',
                'company_id' => $companies->where('name', 'FullTimeZ')->first()->id,
                'category_id' => $categories->where('name', 'Information Technology')->first()->id,
                'user_id' => 1,
                'employment_type' => 'contract',
                'experience_level' => 'mid',
                'location_id' => $getLocationId('New York'), // Remote jobs mapped to a default location
                'min_salary' => '60',
                'max_salary' => '80',
                'salary_currency' => 'USD',
                'salary_period' => 'hourly',
                'status' => 'active',
                'benefits' => 'Remote work, Flexible schedule, Project-based work',
                'requirements' => 'Bachelor degree in Computer Science or related field, 3+ years of software development experience, Proficiency in JavaScript, Python, or Java, Experience with version control',
                'remote_work' => true,
                'positions_available' => 1,
                'tags' => ['JavaScript', 'Python', 'Java', 'Software Development', 'Contract']
            ],

            // Internship Jobs
            [
                'title' => 'Marketing Intern',
                'description' => 'We are offering a Marketing Internship opportunity for students or recent graduates. You will assist with marketing campaigns, social media management, and content creation.',
                'company_id' => $companies->where('name', 'Creative Design Studio')->first()->id,
                'category_id' => $categories->where('name', 'Marketing & Sales')->first()->id,
                'user_id' => 1,
                'employment_type' => 'internship',
                'experience_level' => 'entry',
                'location_id' => $getLocationId('Los Angeles'),
                'min_salary' => '15',
                'max_salary' => '20',
                'salary_currency' => 'USD',
                'salary_period' => 'hourly',
                'status' => 'active',
                'benefits' => 'Learning opportunities, Mentorship, Flexible schedule, Potential for full-time employment',
                'requirements' => 'Currently enrolled in or recent graduate of Marketing, Communications, or related field, Basic knowledge of social media platforms, Strong communication skills, Eagerness to learn',
                'remote_work' => false,
                'positions_available' => 2,
                'tags' => ['Internship', 'Marketing', 'Social Media', 'Learning', 'Entry Level']
            ],
        ];

        foreach ($jobs as $job) {
            Job::create($job);
        }

        $this->command->info('Created ' . count($jobs) . ' jobs successfully!');
    }
}