'use client';

import Link from 'next/link';
import { PlusIcon, UserGroupIcon, BriefcaseIcon, DocumentArrowUpIcon } from '@heroicons/react/24/outline';

export default function ServiceCards() {
  const services = [
    {
      id: 1,
      title: 'Post a Job',
      description: 'Find the perfect candidate for your company',
      icon: PlusIcon,
      color: 'text-blue-600',
      bgColor: 'bg-blue-50',
      link: '/post-job'
    },
    {
      id: 2,
      title: 'Hire Freelancer',
      description: 'Connect with skilled freelancers for projects',
      icon: UserGroupIcon,
      color: 'text-green-600',
      bgColor: 'bg-green-50',
      link: '/freelancers'
    },
    {
      id: 3,
      title: 'Hire Fulltime',
      description: 'Build your dream team with professionals',
      icon: BriefcaseIcon,
      color: 'text-purple-600',
      bgColor: 'bg-purple-50',
      link: '/fulltime'
    },
    {
      id: 4,
      title: 'Upload CV',
      description: 'Showcase your skills to employers',
      icon: DocumentArrowUpIcon,
      color: 'text-orange-600',
      bgColor: 'bg-orange-50',
      link: '/upload-cv'
    }
  ];

  return (
    <section className="py-16 bg-white">
      <div className="container mx-auto px-4">
        {/* Section Header */}
        <div className="text-center mb-12">
          <h2 className="text-3xl font-bold text-gray-900 mb-4">
            Our Services
          </h2>
          <p className="text-lg text-gray-600 max-w-2xl mx-auto">
            Everything you need to find talent or your next opportunity
          </p>
        </div>

        {/* Services Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {services.map((service) => (
            <div key={service.id} className="group">
              <Link href={service.link} className="block">
                <div className="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 hover:border-gray-200">
                  {/* Icon */}
                  <div className={`w-12 h-12 ${service.bgColor} rounded-lg flex items-center justify-center mb-4`}>
                    <service.icon className={`w-6 h-6 ${service.color}`} />
                  </div>

                  {/* Content */}
                  <h3 className="text-lg font-semibold text-gray-900 mb-2">{service.title}</h3>
                  <p className="text-gray-600 text-sm leading-relaxed mb-4">
                    {service.description}
                  </p>

                  {/* CTA */}
                  <div className="text-sm font-medium text-blue-600 group-hover:text-blue-700">
                    Get Started →
                  </div>
                </div>
              </Link>
            </div>
          ))}
        </div>

        {/* Bottom CTA */}
        <div className="text-center mt-12">
          <div className="bg-gray-50 rounded-lg p-8">
            <h3 className="text-2xl font-bold text-gray-900 mb-4">
              Ready to Get Started?
            </h3>
            <p className="text-gray-600 mb-6">
              Join thousands of professionals using our platform
            </p>

            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link
                href="/register?type=employer"
                className="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
              >
                Hire Talent
              </Link>

              <Link
                href="/register?type=jobseeker"
                className="px-6 py-3 bg-white text-blue-600 border-2 border-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors"
              >
                Find Jobs
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
