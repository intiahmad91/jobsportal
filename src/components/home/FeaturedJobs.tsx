'use client';

import Link from 'next/link';
import { MapPinIcon, BuildingOfficeIcon, ClockIcon, CurrencyDollarIcon } from '@heroicons/react/24/outline';

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

interface FeaturedJobsProps {
  jobs: Job[];
  loading: boolean;
}

export default function FeaturedJobs({ jobs, loading }: FeaturedJobsProps) {
  if (loading) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {[...Array(6)].map((_, index) => (
          <div key={index} className="bg-white rounded-lg p-6 shadow-sm animate-pulse">
            <div className="flex items-center mb-4">
              <div className="w-10 h-10 bg-gray-200 rounded-lg mr-3"></div>
              <div className="flex-1">
                <div className="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div className="h-3 bg-gray-200 rounded w-1/2"></div>
              </div>
            </div>
            <div className="h-5 bg-gray-200 rounded w-full mb-3"></div>
            <div className="space-y-2 mb-4">
              <div className="h-4 bg-gray-200 rounded w-full"></div>
              <div className="h-4 bg-gray-200 rounded w-3/4"></div>
            </div>
            <div className="h-4 bg-gray-200 rounded w-1/3"></div>
          </div>
        ))}
      </div>
    );
  }

  if (!jobs || jobs.length === 0) {
    return (
      <div className="text-center py-12">
        <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i className="fas fa-briefcase text-2xl text-blue-600"></i>
        </div>
        <h3 className="text-xl font-bold text-gray-700 mb-2">No Featured Jobs Yet</h3>
        <p className="text-gray-500 mb-6">Check back soon for exciting opportunities!</p>
        <Link
          href="/jobs"
          className="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
        >
          Browse All Jobs
        </Link>
      </div>
    );
  }

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric'
    });
  };

  const getSalaryDisplay = (job: Job) => {
    if (job.min_salary && job.max_salary) {
      return `${job.min_salary} - ${job.max_salary} ${job.salary_currency}`;
    } else if (job.min_salary) {
      return `${job.min_salary}+ ${job.salary_currency}`;
    }
    return 'Salary not specified';
  };

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {jobs.map((job) => (
        <div key={job.id} className="group bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 hover:border-gray-200">
          {/* Job Header */}
          <div className="p-6">
            <div className="flex items-start justify-between mb-4">
              <div className="flex items-center space-x-3">
                <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                  {job.company.logo ? (
                    <img src={job.company.logo} alt={job.company.name} className="w-6 h-6 object-contain" />
                  ) : (
                    <BuildingOfficeIcon className="w-5 h-5 text-blue-600" />
                  )}
                </div>
                <div>
                  <p className="text-sm font-medium text-blue-600">{job.company.name}</p>
                  <p className="text-xs text-gray-500">{formatDate(job.created_at)}</p>
                </div>
              </div>

              {/* Badges */}
              <div className="flex flex-col items-end space-y-1">
                {job.is_premium && (
                  <span className="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                    Premium
                  </span>
                )}
                {job.is_featured && (
                  <span className="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                    Featured
                  </span>
                )}
              </div>
            </div>

            {/* Job Title */}
            <h3 className="text-lg font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
              <Link href={`/jobs/${job.id}`} className="hover:text-blue-600">
                {job.title}
              </Link>
            </h3>

            {/* Job Details */}
            <div className="space-y-2 mb-4">
              <div className="flex items-center text-sm text-gray-600">
                <MapPinIcon className="w-4 h-4 mr-2 text-blue-500" />
                <span>{job.location.city}, {job.location.country}</span>
              </div>

              <div className="flex items-center text-sm text-gray-600">
                <ClockIcon className="w-4 h-4 mr-2 text-green-500" />
                <span className="capitalize">{job.employment_type.replace('_', ' ')}</span>
              </div>
            </div>

            {/* Salary */}
            <div className="mb-4">
              <div className="flex items-center text-sm font-medium text-green-600">
                <CurrencyDollarIcon className="w-4 h-4 mr-1" />
                {getSalaryDisplay(job)}
              </div>
            </div>

            {/* Action Button */}
            <div className="flex justify-between items-center">
              <Link
                href={`/jobs/${job.id}`}
                className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm"
              >
                View Details
              </Link>

              <button className="text-gray-400 hover:text-red-500 transition-colors p-1">
                <i className="far fa-heart text-lg"></i>
              </button>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
