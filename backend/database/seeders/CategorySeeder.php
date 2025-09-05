<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'description' => 'Software development, IT, and technology-related positions',
                'slug' => 'technology',
                'is_active' => true,
            ],
            [
                'name' => 'Healthcare',
                'description' => 'Medical, healthcare, and wellness positions',
                'slug' => 'healthcare',
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'description' => 'Banking, finance, and accounting positions',
                'slug' => 'finance',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'description' => 'Marketing, advertising, and communications positions',
                'slug' => 'marketing',
                'is_active' => true,
            ],
            [
                'name' => 'Education',
                'description' => 'Teaching, training, and educational positions',
                'slug' => 'education',
                'is_active' => true,
            ],
            [
                'name' => 'Sales',
                'description' => 'Sales, business development, and customer service positions',
                'slug' => 'sales',
                'is_active' => true,
            ],
            [
                'name' => 'Design',
                'description' => 'Graphic design, UI/UX, and creative positions',
                'slug' => 'design',
                'is_active' => true,
            ],
            [
                'name' => 'Engineering',
                'description' => 'Mechanical, electrical, and civil engineering positions',
                'slug' => 'engineering',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'description' => 'HR, recruitment, and people operations positions',
                'slug' => 'human-resources',
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'description' => 'Operations, logistics, and supply chain positions',
                'slug' => 'operations',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}