<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = [
            [
                'title' => 'Senior Full Stack Developer',
                'description' => 'We are looking for an experienced full stack developer to join our team. You will be responsible for developing and maintaining web applications using modern technologies.',
                'company_id' => 1,
                'category_id' => 1,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'senior',
                'location_id' => 1,
                'min_salary' => '80000',
                'max_salary' => '120000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Dental insurance, Vision insurance, 401(k) matching, Flexible work hours, Remote work options',
                'requirements' => 'Bachelor degree in Computer Science or related field, 5+ years of experience in full stack development, Proficiency in React, Node.js, and database management',
                'remote_work' => true
            ],
            [
                'title' => 'Marketing Manager',
                'description' => 'Join our marketing team as a Marketing Manager. You will be responsible for developing and implementing marketing strategies to promote our products and services.',
                'company_id' => 2,
                'category_id' => 2,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location_id' => 2,
                'min_salary' => '60000',
                'max_salary' => '80000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Paid time off, Professional development opportunities, Team building events',
                'requirements' => 'Bachelor degree in Marketing or related field, 3+ years of marketing experience, Strong communication and analytical skills'
            ],
            [
                'title' => 'Data Scientist',
                'description' => 'We are seeking a talented Data Scientist to analyze complex data sets and provide insights to drive business decisions.',
                'company_id' => 3,
                'category_id' => 1,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'senior',
                'location_id' => 3,
                'min_salary' => '100000',
                'max_salary' => '150000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Stock options, Flexible work schedule, Learning and development budget',
                'requirements' => 'Master degree in Data Science, Statistics, or related field, 4+ years of experience in data analysis, Proficiency in Python, R, and machine learning'
            ],
            [
                'title' => 'UX/UI Designer',
                'description' => 'Join our design team as a UX/UI Designer. You will create user-centered designs for our digital products and ensure excellent user experience.',
                'company_id' => 4,
                'category_id' => 3,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location_id' => 4,
                'min_salary' => '70000',
                'max_salary' => '95000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, Dental insurance, Vision insurance, Flexible work hours, Creative workspace',
                'requirements' => 'Bachelor degree in Design or related field, 3+ years of UX/UI design experience, Proficiency in Figma, Adobe Creative Suite, and prototyping tools'
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'We are looking for a DevOps Engineer to help us build and maintain our cloud infrastructure and deployment pipelines.',
                'company_id' => 5,
                'category_id' => 1,
                'user_id' => 1,
                'employment_type' => 'full_time',
                'experience_level' => 'senior',
                'location_id' => 5,
                'min_salary' => '90000',
                'max_salary' => '130000',
                'salary_currency' => 'USD',
                'salary_period' => 'yearly',
                'status' => 'active',
                'benefits' => 'Health insurance, 401(k) matching, Remote work options, Professional development budget, Flexible PTO',
                'requirements' => 'Bachelor degree in Computer Science or related field, 4+ years of DevOps experience, Experience with AWS, Docker, Kubernetes, and CI/CD pipelines'
            ]
        ];

        foreach ($jobs as $job) {
            Job::create($job);
        }
    }
}