<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Skill;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Programming Languages
            ['name' => 'JavaScript', 'category' => 'Programming Languages', 'icon' => 'fab fa-js-square', 'color' => '#F7DF1E'],
            ['name' => 'Python', 'category' => 'Programming Languages', 'icon' => 'fab fa-python', 'color' => '#3776AB'],
            ['name' => 'Java', 'category' => 'Programming Languages', 'icon' => 'fab fa-java', 'color' => '#ED8B00'],
            ['name' => 'C++', 'category' => 'Programming Languages', 'icon' => 'fas fa-code', 'color' => '#00599C'],
            ['name' => 'C Sharp', 'category' => 'Programming Languages', 'icon' => 'fas fa-code', 'color' => '#239120'],
            ['name' => 'PHP', 'category' => 'Programming Languages', 'icon' => 'fab fa-php', 'color' => '#777BB4'],
            ['name' => 'Ruby', 'category' => 'Programming Languages', 'icon' => 'fas fa-gem', 'color' => '#CC342D'],
            ['name' => 'Go', 'category' => 'Programming Languages', 'icon' => 'fas fa-code', 'color' => '#00ADD8'],
            ['name' => 'Swift', 'category' => 'Programming Languages', 'icon' => 'fab fa-swift', 'color' => '#FA7343'],
            ['name' => 'Kotlin', 'category' => 'Programming Languages', 'icon' => 'fas fa-code', 'color' => '#0095D5'],
            
            // Web Technologies
            ['name' => 'HTML5', 'category' => 'Web Technologies', 'icon' => 'fab fa-html5', 'color' => '#E34F26'],
            ['name' => 'CSS3', 'category' => 'Web Technologies', 'icon' => 'fab fa-css3-alt', 'color' => '#1572B6'],
            ['name' => 'React', 'category' => 'Web Technologies', 'icon' => 'fab fa-react', 'color' => '#61DAFB'],
            ['name' => 'Angular', 'category' => 'Web Technologies', 'icon' => 'fab fa-angular', 'color' => '#DD0031'],
            ['name' => 'Vue.js', 'category' => 'Web Technologies', 'icon' => 'fab fa-vuejs', 'color' => '#4FC08D'],
            ['name' => 'Node.js', 'category' => 'Web Technologies', 'icon' => 'fab fa-node-js', 'color' => '#339933'],
            ['name' => 'Express.js', 'category' => 'Web Technologies', 'icon' => 'fas fa-server', 'color' => '#000000'],
            ['name' => 'Django', 'category' => 'Web Technologies', 'icon' => 'fab fa-python', 'color' => '#092E20'],
            ['name' => 'Laravel', 'category' => 'Web Technologies', 'icon' => 'fab fa-laravel', 'color' => '#FF2D20'],
            ['name' => 'Spring Boot', 'category' => 'Web Technologies', 'icon' => 'fas fa-leaf', 'color' => '#6DB33F'],
            
            // Databases
            ['name' => 'MySQL', 'category' => 'Databases', 'icon' => 'fas fa-database', 'color' => '#4479A1'],
            ['name' => 'PostgreSQL', 'category' => 'Databases', 'icon' => 'fas fa-database', 'color' => '#336791'],
            ['name' => 'MongoDB', 'category' => 'Databases', 'icon' => 'fas fa-database', 'color' => '#47A248'],
            ['name' => 'Redis', 'category' => 'Databases', 'icon' => 'fas fa-database', 'color' => '#DC382D'],
            ['name' => 'SQLite', 'category' => 'Databases', 'icon' => 'fas fa-database', 'color' => '#003B57'],
            ['name' => 'Oracle', 'category' => 'Databases', 'icon' => 'fas fa-database', 'color' => '#F80000'],
            ['name' => 'SQL Server', 'category' => 'Databases', 'icon' => 'fas fa-database', 'color' => '#CC2927'],
            
            // Cloud & DevOps
            ['name' => 'AWS', 'category' => 'Cloud & DevOps', 'icon' => 'fab fa-aws', 'color' => '#FF9900'],
            ['name' => 'Azure', 'category' => 'Cloud & DevOps', 'icon' => 'fab fa-microsoft', 'color' => '#0089D6'],
            ['name' => 'Google Cloud', 'category' => 'Cloud & DevOps', 'icon' => 'fab fa-google', 'color' => '#4285F4'],
            ['name' => 'Docker', 'category' => 'Cloud & DevOps', 'icon' => 'fab fa-docker', 'color' => '#2496ED'],
            ['name' => 'Kubernetes', 'category' => 'Cloud & DevOps', 'icon' => 'fas fa-ship', 'color' => '#326CE5'],
            ['name' => 'Jenkins', 'category' => 'Cloud & DevOps', 'icon' => 'fas fa-cog', 'color' => '#D24939'],
            ['name' => 'Git', 'category' => 'Cloud & DevOps', 'icon' => 'fab fa-git-alt', 'color' => '#F05032'],
            ['name' => 'GitHub', 'category' => 'Cloud & DevOps', 'icon' => 'fab fa-github', 'color' => '#181717'],
            ['name' => 'GitLab', 'category' => 'Cloud & DevOps', 'icon' => 'fab fa-gitlab', 'color' => '#FCA326'],
            
            // Mobile Development
            ['name' => 'React Native', 'category' => 'Mobile Development', 'icon' => 'fab fa-react', 'color' => '#61DAFB'],
            ['name' => 'Flutter', 'category' => 'Mobile Development', 'icon' => 'fas fa-mobile-alt', 'color' => '#02569B'],
            ['name' => 'Xamarin', 'category' => 'Mobile Development', 'icon' => 'fab fa-microsoft', 'color' => '#3498DB'],
            ['name' => 'Ionic', 'category' => 'Mobile Development', 'icon' => 'fas fa-mobile-alt', 'color' => '#3880FF'],
            
            // Data Science & AI
            ['name' => 'Machine Learning', 'category' => 'Data Science & AI', 'icon' => 'fas fa-brain', 'color' => '#FF6B6B'],
            ['name' => 'Deep Learning', 'category' => 'Data Science & AI', 'icon' => 'fas fa-network-wired', 'color' => '#4ECDC4'],
            ['name' => 'TensorFlow', 'category' => 'Data Science & AI', 'icon' => 'fas fa-brain', 'color' => '#FF6F00'],
            ['name' => 'PyTorch', 'category' => 'Data Science & AI', 'icon' => 'fas fa-fire', 'color' => '#EE4C2C'],
            ['name' => 'Pandas', 'category' => 'Data Science & AI', 'icon' => 'fas fa-table', 'color' => '#130654'],
            ['name' => 'NumPy', 'category' => 'Data Science & AI', 'icon' => 'fas fa-calculator', 'color' => '#4DABCF'],
            ['name' => 'Scikit-learn', 'category' => 'Data Science & AI', 'icon' => 'fas fa-chart-line', 'color' => '#F7931E'],
            
            // Design & Creative
            ['name' => 'Adobe Photoshop', 'category' => 'Design & Creative', 'icon' => 'fab fa-adobe', 'color' => '#31A8FF'],
            ['name' => 'Adobe Illustrator', 'category' => 'Design & Creative', 'icon' => 'fab fa-adobe', 'color' => '#FF9A00'],
            ['name' => 'Adobe XD', 'category' => 'Design & Creative', 'icon' => 'fab fa-adobe', 'color' => '#FF61F6'],
            ['name' => 'Figma', 'category' => 'Design & Creative', 'icon' => 'fab fa-figma', 'color' => '#F24E1E'],
            ['name' => 'Sketch', 'category' => 'Design & Creative', 'icon' => 'fab fa-sketch', 'color' => '#F7B500'],
            ['name' => 'InVision', 'category' => 'Design & Creative', 'icon' => 'fas fa-eye', 'color' => '#FF3366'],
            
            // Business & Office
            ['name' => 'Microsoft Excel', 'category' => 'Business & Office', 'icon' => 'fas fa-table', 'color' => '#217346'],
            ['name' => 'Microsoft Word', 'category' => 'Business & Office', 'icon' => 'fas fa-file-word', 'color' => '#2B579A'],
            ['name' => 'Microsoft PowerPoint', 'category' => 'Business & Office', 'icon' => 'fas fa-presentation', 'color' => '#D24726'],
            ['name' => 'Salesforce', 'category' => 'Business & Office', 'icon' => 'fab fa-salesforce', 'color' => '#00A1E0'],
            ['name' => 'HubSpot', 'category' => 'Business & Office', 'icon' => 'fas fa-hubspot', 'color' => '#FF7A59'],
            ['name' => 'Slack', 'category' => 'Business & Office', 'icon' => 'fab fa-slack', 'color' => '#4A154B'],
            ['name' => 'Trello', 'category' => 'Business & Office', 'icon' => 'fab fa-trello', 'color' => '#0079BF'],
            ['name' => 'Asana', 'category' => 'Business & Office', 'icon' => 'fas fa-tasks', 'color' => '#F06A6A'],
            
            // Marketing & Analytics
            ['name' => 'Google Analytics', 'category' => 'Marketing & Analytics', 'icon' => 'fab fa-google', 'color' => '#E37400'],
            ['name' => 'Google Ads', 'category' => 'Marketing & Analytics', 'icon' => 'fab fa-google', 'color' => '#4285F4'],
            ['name' => 'Facebook Ads', 'category' => 'Marketing & Analytics', 'icon' => 'fab fa-facebook', 'color' => '#1877F2'],
            ['name' => 'SEO', 'category' => 'Marketing & Analytics', 'icon' => 'fas fa-search', 'color' => '#FF6B35'],
            ['name' => 'Content Marketing', 'category' => 'Marketing & Analytics', 'icon' => 'fas fa-edit', 'color' => '#FF6B6B'],
            ['name' => 'Social Media Marketing', 'category' => 'Marketing & Analytics', 'icon' => 'fas fa-share-alt', 'color' => '#4ECDC4'],
            
            // Languages
            ['name' => 'English', 'category' => 'Languages', 'icon' => 'fas fa-language', 'color' => '#1E40AF'],
            ['name' => 'Spanish', 'category' => 'Languages', 'icon' => 'fas fa-language', 'color' => '#DC2626'],
            ['name' => 'French', 'category' => 'Languages', 'icon' => 'fas fa-language', 'color' => '#7C3AED'],
            ['name' => 'German', 'category' => 'Languages', 'icon' => 'fas fa-language', 'color' => '#059669'],
            ['name' => 'Chinese', 'category' => 'Languages', 'icon' => 'fas fa-language', 'color' => '#DC2626'],
            ['name' => 'Japanese', 'category' => 'Languages', 'icon' => 'fas fa-language', 'color' => '#1F2937'],
            ['name' => 'Arabic', 'category' => 'Languages', 'icon' => 'fas fa-language', 'color' => '#059669'],
            ['name' => 'Russian', 'category' => 'Languages', 'icon' => 'fas fa-language', 'color' => '#1E40AF'],
        ];

        foreach ($skills as $skill) {
            Skill::create([
                'name' => $skill['name'],
                'slug' => Str::slug($skill['name']),
                'description' => $skill['name'] . ' skill and expertise',
                'category' => $skill['category'],
                'icon' => $skill['icon'],
                'color' => $skill['color'],
                'is_active' => true,
                'usage_count' => 0,
            ]);
        }
    }
}
