'use client';

import { useState, useEffect } from 'react';
import Header from '@/components/layout/Header';
import HeroSection from '@/components/home/HeroSection';
import ServiceCards from '@/components/home/ServiceCards';
import FeaturedJobs from '@/components/home/FeaturedJobs';
import BookMeeting from '@/components/home/BookMeeting';
import Footer from '@/components/layout/Footer';

interface Job {
  id: number;
  title: string;
  company: {
    name: string;
    logo?: string;
  };
  location: {
    city: string;
    country: string;
  };
  employment_type: string;
  experience_level: string;
  min_salary?: string;
  max_salary?: string;
  salary_currency: string;
  salary_period: string;
  is_premium: boolean;
  is_featured: boolean;
  created_at: string;
}

export default function Home() {
  const [featuredJobs, setFeaturedJobs] = useState<Job[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Simulate API call - in real app, this would fetch from Laravel backend
    const fetchJobs = async () => {
      try {
        // Mock data for demonstration
        const mockFeaturedJobs: Job[] = [
          {
            id: 1,
            title: "Senior Frontend Developer",
            company: { name: "TechCorp Inc.", logo: "/images/placeholder.svg" },
            location: { city: "San Francisco", country: "USA" },
            employment_type: "full_time",
            experience_level: "senior",
            min_salary: "80000",
            max_salary: "120000",
            salary_currency: "USD",
            salary_period: "yearly",
            is_premium: true,
            is_featured: true,
            created_at: "2024-01-15T10:00:00Z"
          },
          {
            id: 2,
            title: "UX/UI Designer",
            company: { name: "Design Studio", logo: "/images/placeholder.svg" },
            location: { city: "New York", country: "USA" },
            employment_type: "full_time",
            experience_level: "mid_level",
            min_salary: "70000",
            max_salary: "100000",
            salary_currency: "USD",
            salary_period: "yearly",
            is_premium: false,
            is_featured: true,
            created_at: "2024-01-14T15:30:00Z"
          },
          {
            id: 3,
            title: "Full Stack Engineer",
            company: { name: "StartupXYZ", logo: "/images/placeholder.svg" },
            location: { city: "Austin", country: "USA" },
            employment_type: "full_time",
            experience_level: "senior",
            min_salary: "90000",
            max_salary: "130000",
            salary_currency: "USD",
            salary_period: "yearly",
            is_premium: true,
            is_featured: false,
            created_at: "2024-01-13T09:15:00Z"
          },
          {
            id: 4,
            title: "DevOps Engineer",
            company: { name: "CloudTech Solutions", logo: "/images/placeholder.svg" },
            location: { city: "Seattle", country: "USA" },
            employment_type: "full_time",
            experience_level: "mid_level",
            min_salary: "85000",
            max_salary: "110000",
            salary_currency: "USD",
            salary_period: "yearly",
            is_premium: false,
            is_featured: true,
            created_at: "2024-01-12T14:45:00Z"
          },
          {
            id: 5,
            title: "Data Scientist",
            company: { name: "Analytics Pro", logo: "/images/placeholder.svg" },
            location: { city: "Boston", country: "USA" },
            employment_type: "full_time",
            experience_level: "senior",
            min_salary: "95000",
            max_salary: "140000",
            salary_currency: "USD",
            salary_period: "yearly",
            is_premium: true,
            is_featured: false,
            created_at: "2024-01-11T11:20:00Z"
          },
          {
            id: 6,
            title: "Product Manager",
            company: { name: "Innovation Labs", logo: "/images/placeholder.svg" },
            location: { city: "San Diego", country: "USA" },
            employment_type: "full_time",
            experience_level: "senior",
            min_salary: "100000",
            max_salary: "150000",
            salary_currency: "USD",
            salary_period: "yearly",
            is_premium: false,
            is_featured: true,
            created_at: "2024-01-10T16:30:00Z"
          }
        ];

        // Simulate loading delay
        setTimeout(() => {
          setFeaturedJobs(mockFeaturedJobs);
          setLoading(false);
        }, 1000);
      } catch (error) {
        console.error('Error fetching jobs:', error);
        setLoading(false);
      }
    };

    fetchJobs();
  }, []);

  return (
    <main className="min-h-screen">
      <Header />

      <HeroSection />

      <ServiceCards />

      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">
              Featured
              <span className="text-blue-600 block">Job Opportunities</span>
            </h2>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
              Discover the most exciting positions from top companies worldwide
            </p>
          </div>
          <FeaturedJobs jobs={featuredJobs} loading={loading} />
        </div>
      </section>

      <BookMeeting />

      <Footer />
    </main>
  );
}
