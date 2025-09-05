'use client';

import { useState } from 'react';
import { ChevronDownIcon, MagnifyingGlassIcon, PlusIcon } from '@heroicons/react/24/outline';

export default function HeroSection() {
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);

  return (
    <section className="relative py-20 bg-gradient-to-br from-blue-600 to-indigo-700 overflow-hidden">
      {/* Simple Background Pattern */}
      <div className="absolute inset-0 opacity-10">
        <div className="absolute top-10 left-10 w-32 h-32 bg-white rounded-full"></div>
        <div className="absolute bottom-10 right-10 w-24 h-24 bg-white rounded-full"></div>
      </div>

      <div className="container mx-auto px-4 relative z-10">
        <div className="max-w-4xl mx-auto text-center">
          {/* Main Title */}
          <div className="mb-8">
            <h1 className="text-4xl lg:text-5xl font-bold text-white mb-4">
              Find Your Dream Job
            </h1>
            <p className="text-lg lg:text-xl text-blue-100 leading-relaxed">
              Connect with top employers and discover opportunities that match your skills
            </p>
          </div>

          {/* Simple Search Bar */}
          <div className="mb-8">
            <div className="flex flex-col sm:flex-row gap-3 max-w-2xl mx-auto">
              <input 
                type="text" 
                placeholder="Job title or keyword" 
                className="flex-1 px-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-white focus:ring-opacity-50"
              />
              <input 
                type="text" 
                placeholder="Location" 
                className="flex-1 px-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-white focus:ring-opacity-50"
              />
              <button className="px-8 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Search Jobs
              </button>
            </div>
          </div>

          {/* Simple Stats */}
          <div className="grid grid-cols-3 gap-8 max-w-md mx-auto">
            <div className="text-center">
              <div className="text-2xl font-bold text-white mb-1">50K+</div>
              <div className="text-blue-200 text-sm">Active Users</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-white mb-1">25K+</div>
              <div className="text-blue-200 text-sm">Jobs Posted</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-white mb-1">150+</div>
              <div className="text-blue-200 text-sm">Countries</div>
            </div>
          </div>

          {/* Simple CTA Buttons */}
          <div className="flex flex-col sm:flex-row gap-4 justify-center mt-8">
            <button className="px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
              <PlusIcon className="w-5 h-5 inline mr-2" />
              Post a Job
            </button>
            <button className="px-6 py-3 bg-transparent text-white border-2 border-white rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
              Upload CV
            </button>
          </div>
        </div>
      </div>
    </section>
  );
}
