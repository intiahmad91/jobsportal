<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobCategory;
use Illuminate\Support\Str;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Information Technology',
                'description' => 'Software development, IT support, and technology roles',
                'icon' => 'fas fa-laptop-code',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Healthcare',
                'description' => 'Medical, nursing, and healthcare administration roles',
                'icon' => 'fas fa-heartbeat',
                'color' => '#EF4444',
                'sort_order' => 2,
            ],
            [
                'name' => 'Finance & Accounting',
                'description' => 'Banking, accounting, and financial services roles',
                'icon' => 'fas fa-chart-line',
                'color' => '#10B981',
                'sort_order' => 3,
            ],
            [
                'name' => 'Marketing & Sales',
                'description' => 'Digital marketing, sales, and business development roles',
                'icon' => 'fas fa-bullhorn',
                'color' => '#F59E0B',
                'sort_order' => 4,
            ],
            [
                'name' => 'Education & Training',
                'description' => 'Teaching, training, and educational administration roles',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#8B5CF6',
                'sort_order' => 5,
            ],
            [
                'name' => 'Engineering',
                'description' => 'Civil, mechanical, electrical, and other engineering roles',
                'icon' => 'fas fa-cogs',
                'color' => '#6B7280',
                'sort_order' => 6,
            ],
            [
                'name' => 'Design & Creative',
                'description' => 'Graphic design, UI/UX, and creative arts roles',
                'icon' => 'fas fa-palette',
                'color' => '#EC4899',
                'sort_order' => 7,
            ],
            [
                'name' => 'Human Resources',
                'description' => 'HR management, recruitment, and employee relations roles',
                'icon' => 'fas fa-users',
                'color' => '#06B6D4',
                'sort_order' => 8,
            ],
            [
                'name' => 'Customer Service',
                'description' => 'Customer support, call center, and service roles',
                'icon' => 'fas fa-headset',
                'color' => '#84CC16',
                'sort_order' => 9,
            ],
            [
                'name' => 'Administration',
                'description' => 'Office administration, secretarial, and clerical roles',
                'icon' => 'fas fa-clipboard-list',
                'color' => '#F97316',
                'sort_order' => 10,
            ],
            [
                'name' => 'Legal',
                'description' => 'Law, paralegal, and legal support roles',
                'icon' => 'fas fa-balance-scale',
                'color' => '#1F2937',
                'sort_order' => 11,
            ],
            [
                'name' => 'Media & Communications',
                'description' => 'Journalism, public relations, and media production roles',
                'icon' => 'fas fa-newspaper',
                'color' => '#7C3AED',
                'sort_order' => 12,
            ],
            [
                'name' => 'Transportation & Logistics',
                'description' => 'Shipping, delivery, and supply chain roles',
                'icon' => 'fas fa-truck',
                'color' => '#059669',
                'sort_order' => 13,
            ],
            [
                'name' => 'Hospitality & Tourism',
                'description' => 'Hotel, restaurant, and travel industry roles',
                'icon' => 'fas fa-hotel',
                'color' => '#DC2626',
                'sort_order' => 14,
            ],
            [
                'name' => 'Retail & E-commerce',
                'description' => 'Store management, sales, and online retail roles',
                'icon' => 'fas fa-shopping-cart',
                'color' => '#EA580C',
                'sort_order' => 15,
            ],
        ];

        foreach ($categories as $category) {
            JobCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_active' => true,
                'sort_order' => $category['sort_order'],
            ]);
        }
    }
}
