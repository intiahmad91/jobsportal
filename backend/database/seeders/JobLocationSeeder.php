<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobLocation;
use Illuminate\Support\Str;

class JobLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            // United States
            ['city' => 'New York', 'state' => 'NY', 'country' => 'United States', 'latitude' => 40.7128, 'longitude' => -74.0060],
            ['city' => 'Los Angeles', 'state' => 'CA', 'country' => 'United States', 'latitude' => 34.0522, 'longitude' => -118.2437],
            ['city' => 'Chicago', 'state' => 'IL', 'country' => 'United States', 'latitude' => 41.8781, 'longitude' => -87.6298],
            ['city' => 'Houston', 'state' => 'TX', 'country' => 'United States', 'latitude' => 29.7604, 'longitude' => -95.3698],
            ['city' => 'Phoenix', 'state' => 'AZ', 'country' => 'United States', 'latitude' => 33.4484, 'longitude' => -112.0740],
            ['city' => 'Philadelphia', 'state' => 'PA', 'country' => 'United States', 'latitude' => 39.9526, 'longitude' => -75.1652],
            ['city' => 'San Antonio', 'state' => 'TX', 'country' => 'United States', 'latitude' => 29.4241, 'longitude' => -98.4936],
            ['city' => 'San Diego', 'state' => 'CA', 'country' => 'United States', 'latitude' => 32.7157, 'longitude' => -117.1611],
            ['city' => 'Dallas', 'state' => 'TX', 'country' => 'United States', 'latitude' => 32.7767, 'longitude' => -96.7970],
            ['city' => 'San Jose', 'state' => 'CA', 'country' => 'United States', 'latitude' => 37.3382, 'longitude' => -121.8863],
            ['city' => 'California', 'state' => 'CA', 'country' => 'United States', 'latitude' => 36.7783, 'longitude' => -119.4179],
            
            // Canada
            ['city' => 'Toronto', 'state' => 'ON', 'country' => 'Canada', 'latitude' => 43.6532, 'longitude' => -79.3832],
            ['city' => 'Montreal', 'state' => 'QC', 'country' => 'Canada', 'latitude' => 45.5017, 'longitude' => -73.5673],
            ['city' => 'Vancouver', 'state' => 'BC', 'country' => 'Canada', 'latitude' => 49.2827, 'longitude' => -123.1207],
            ['city' => 'Calgary', 'state' => 'AB', 'country' => 'Canada', 'latitude' => 51.0447, 'longitude' => -114.0719],
            ['city' => 'Edmonton', 'state' => 'AB', 'country' => 'Canada', 'latitude' => 53.5461, 'longitude' => -113.4938],
            
            // United Kingdom
            ['city' => 'London', 'state' => null, 'country' => 'United Kingdom', 'latitude' => 51.5074, 'longitude' => -0.1278],
            ['city' => 'Manchester', 'state' => null, 'country' => 'United Kingdom', 'latitude' => 53.4808, 'longitude' => -2.2426],
            ['city' => 'Birmingham', 'state' => null, 'country' => 'United Kingdom', 'latitude' => 52.4862, 'longitude' => -1.8904],
            ['city' => 'Leeds', 'state' => null, 'country' => 'United Kingdom', 'latitude' => 53.8008, 'longitude' => -1.5491],
            ['city' => 'Liverpool', 'state' => null, 'country' => 'United Kingdom', 'latitude' => 53.4084, 'longitude' => -2.9916],
            
            // Australia
            ['city' => 'Sydney', 'state' => 'NSW', 'country' => 'Australia', 'latitude' => -33.8688, 'longitude' => 151.2093],
            ['city' => 'Melbourne', 'state' => 'VIC', 'country' => 'Australia', 'latitude' => -37.8136, 'longitude' => 144.9631],
            ['city' => 'Brisbane', 'state' => 'QLD', 'country' => 'Australia', 'latitude' => -27.4698, 'longitude' => 153.0251],
            ['city' => 'Perth', 'state' => 'WA', 'country' => 'Australia', 'latitude' => -31.9505, 'longitude' => 115.8605],
            ['city' => 'Adelaide', 'state' => 'SA', 'country' => 'Australia', 'latitude' => -34.9285, 'longitude' => 138.6007],
            
            // Germany
            ['city' => 'Berlin', 'state' => null, 'country' => 'Germany', 'latitude' => 52.5200, 'longitude' => 13.4050],
            ['city' => 'Hamburg', 'state' => null, 'country' => 'Germany', 'latitude' => 53.5511, 'longitude' => 9.9937],
            ['city' => 'Munich', 'state' => null, 'country' => 'Germany', 'latitude' => 48.1351, 'longitude' => 11.5820],
            ['city' => 'Cologne', 'state' => null, 'country' => 'Germany', 'latitude' => 50.9375, 'longitude' => 6.9603],
            ['city' => 'Frankfurt', 'state' => null, 'country' => 'Germany', 'latitude' => 50.1109, 'longitude' => 8.6821],
            
            // France
            ['city' => 'Paris', 'state' => null, 'country' => 'France', 'latitude' => 48.8566, 'longitude' => 2.3522],
            ['city' => 'Marseille', 'state' => null, 'country' => 'France', 'latitude' => 43.2965, 'longitude' => 5.3698],
            ['city' => 'Lyon', 'state' => null, 'country' => 'France', 'latitude' => 45.7640, 'longitude' => 4.8357],
            ['city' => 'Toulouse', 'state' => null, 'country' => 'France', 'latitude' => 43.6047, 'longitude' => 1.4442],
            ['city' => 'Nice', 'state' => null, 'country' => 'France', 'latitude' => 43.7102, 'longitude' => 7.2620],
            
            // Netherlands
            ['city' => 'Amsterdam', 'state' => null, 'country' => 'Netherlands', 'latitude' => 52.3676, 'longitude' => 4.9041],
            ['city' => 'Rotterdam', 'state' => null, 'country' => 'Netherlands', 'latitude' => 51.9225, 'longitude' => 4.4792],
            ['city' => 'The Hague', 'state' => null, 'country' => 'Netherlands', 'latitude' => 52.0705, 'longitude' => 4.3007],
            ['city' => 'Utrecht', 'state' => null, 'country' => 'Netherlands', 'latitude' => 52.0907, 'longitude' => 5.1214],
            
            // Singapore
            ['city' => 'Singapore', 'state' => null, 'country' => 'Singapore', 'latitude' => 1.3521, 'longitude' => 103.8198],
            
            // Japan
            ['city' => 'Tokyo', 'state' => null, 'country' => 'Japan', 'latitude' => 35.6762, 'longitude' => 139.6503],
            ['city' => 'Osaka', 'state' => null, 'country' => 'Japan', 'latitude' => 34.6937, 'longitude' => 135.5023],
            ['city' => 'Kyoto', 'state' => null, 'country' => 'Japan', 'latitude' => 35.0116, 'longitude' => 135.7681],
            
            // India
            ['city' => 'Mumbai', 'state' => 'Maharashtra', 'country' => 'India', 'latitude' => 19.0760, 'longitude' => 72.8777],
            ['city' => 'Delhi', 'state' => 'Delhi', 'country' => 'India', 'latitude' => 28.7041, 'longitude' => 77.1025],
            ['city' => 'Bangalore', 'state' => 'Karnataka', 'country' => 'India', 'latitude' => 12.9716, 'longitude' => 77.5946],
            ['city' => 'Hyderabad', 'state' => 'Telangana', 'country' => 'India', 'latitude' => 17.3850, 'longitude' => 78.4867],
            ['city' => 'Chennai', 'state' => 'Tamil Nadu', 'country' => 'India', 'latitude' => 13.0827, 'longitude' => 80.2707],
        ];

        foreach ($locations as $location) {
            JobLocation::create([
                'city' => $location['city'],
                'state' => $location['state'],
                'country' => $location['country'],
                'slug' => Str::slug($location['city'] . '-' . $location['country']),
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'is_active' => true,
                'job_count' => 0,
            ]);
        }
    }
}
