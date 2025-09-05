'use client';

import { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { Bars3Icon, XMarkIcon, MagnifyingGlassIcon } from '@heroicons/react/24/outline';

export default function Header() {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const router = useRouter();

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (searchQuery.trim()) {
      router.push(`/jobs?search=${encodeURIComponent(searchQuery.trim())}`);
    }
  };

  return (
    <header className="bg-white shadow-sm sticky top-0 z-50">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between py-4">
          {/* Logo */}
          <Link href="/" className="flex items-center space-x-2">
            <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <span className="text-white font-bold text-lg">F</span>
            </div>
            <span className="text-xl font-bold text-gray-900">FullTimez</span>
          </Link>

          {/* Desktop Search */}
          <div className="hidden lg:flex flex-1 max-w-md mx-8">
            <form onSubmit={handleSearch} className="flex w-full">
              <input
                type="text"
                placeholder="Search jobs..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <button
                type="submit"
                className="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors"
              >
                <MagnifyingGlassIcon className="w-4 h-4" />
              </button>
            </form>
          </div>

          {/* Desktop Navigation */}
          <nav className="hidden lg:flex items-center space-x-6">
            <Link href="/jobs" className="text-gray-700 hover:text-blue-600 font-medium transition-colors">
              Jobs
            </Link>
            <Link href="/companies" className="text-gray-700 hover:text-blue-600 font-medium transition-colors">
              Companies
            </Link>
            <Link href="/login" className="text-gray-700 hover:text-blue-600 font-medium transition-colors">
              Login
            </Link>
            <Link href="/register" className="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
              Sign Up
            </Link>
          </nav>

          {/* Mobile Menu Button */}
          <button
            onClick={() => setIsMenuOpen(!isMenuOpen)}
            className="lg:hidden p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 transition-colors"
          >
            {isMenuOpen ? (
              <XMarkIcon className="w-6 h-6" />
            ) : (
              <Bars3Icon className="w-6 h-6" />
            )}
          </button>
        </div>

        {/* Mobile Search */}
        <div className="lg:hidden pb-4">
          <form onSubmit={handleSearch} className="flex">
            <input
              type="text"
              placeholder="Search jobs..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <button
              type="submit"
              className="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors"
            >
              <MagnifyingGlassIcon className="w-4 h-4" />
            </button>
          </form>
        </div>

        {/* Mobile Menu */}
        {isMenuOpen && (
          <div className="lg:hidden pb-4 border-t border-gray-200">
            <nav className="flex flex-col space-y-3 pt-4">
              <Link href="/jobs" className="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                Jobs
              </Link>
              <Link href="/companies" className="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                Companies
              </Link>
              <Link href="/login" className="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                Login
              </Link>
              <Link href="/register" className="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors text-center">
                Sign Up
              </Link>
            </nav>
          </div>
        )}
      </div>
    </header>
  );
}
