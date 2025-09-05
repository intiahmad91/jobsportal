'use client';

export default function Footer() {
  return (
    <footer className="bg-gray-900 text-white">
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {/* Company Info */}
          <div className="lg:col-span-2">
            <div className="mb-6">
              <div className="flex items-center space-x-2 mb-4">
                <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                  <span className="text-white font-bold text-lg">F</span>
                </div>
                <span className="text-xl font-bold">FullTimez</span>
              </div>
              <p className="text-gray-300 text-sm leading-relaxed mb-6">
                Full Timez is your trusted partner in connecting talented professionals with exceptional opportunities.
              </p>
            </div>

            {/* Social Links */}
            <div className="mb-6">
              <h4 className="text-white font-semibold mb-3">Follow Us</h4>
              <div className="flex space-x-4">
                <a className="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors" href="#">
                  <i className="fab fa-facebook-f text-sm"></i>
                </a>
                <a className="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors" href="#">
                  <i className="fab fa-twitter text-sm"></i>
                </a>
                <a className="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors" href="#">
                  <i className="fab fa-linkedin-in text-sm"></i>
                </a>
                <a className="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors" href="#">
                  <i className="fab fa-instagram text-sm"></i>
                </a>
              </div>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-white font-semibold mb-4">Company</h4>
            <ul className="space-y-2">
              <li><a className="text-gray-300 hover:text-white transition-colors text-sm" href="/about">About Us</a></li>
              <li><a className="text-gray-300 hover:text-white transition-colors text-sm" href="/contact">Contact</a></li>
              <li><a className="text-gray-300 hover:text-white transition-colors text-sm" href="/careers">Careers</a></li>
              <li><a className="text-gray-300 hover:text-white transition-colors text-sm" href="/blog">Blog</a></li>
            </ul>
          </div>

          {/* Services */}
          <div>
            <h4 className="text-white font-semibold mb-4">Services</h4>
            <ul className="space-y-2">
              <li><a className="text-gray-300 hover:text-white transition-colors text-sm" href="/post-job">Post a Job</a></li>
              <li><a className="text-gray-300 hover:text-white transition-colors text-sm" href="/jobs">Browse Jobs</a></li>
              <li><a className="text-gray-300 hover:text-white transition-colors text-sm" href="/freelancers">Hire Freelancers</a></li>
              <li><a className="text-gray-300 hover:text-white transition-colors text-sm" href="/upload-cv">Upload CV</a></li>
            </ul>
          </div>
        </div>

        {/* Newsletter */}
        <div className="border-t border-gray-800 mt-8 pt-8">
          <div className="text-center">
            <h3 className="text-lg font-semibold mb-2">Stay Updated</h3>
            <p className="text-gray-300 mb-4 text-sm">Get the latest job opportunities delivered to your inbox.</p>
            <div className="max-w-md mx-auto flex gap-3">
              <input 
                type="email" 
                placeholder="Enter your email address" 
                className="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
              />
              <button className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                Subscribe
              </button>
            </div>
          </div>
        </div>

        {/* Copyright */}
        <div className="border-t border-gray-800 mt-8 pt-6">
          <div className="flex flex-col md:flex-row justify-between items-center">
            <div className="text-gray-400 text-sm mb-4 md:mb-0">
              © 2025 Full Timez. All rights reserved.
            </div>
            <div className="flex items-center space-x-6 text-sm text-gray-400">
              <a className="hover:text-white transition-colors" href="/privacy">Privacy Policy</a>
              <a className="hover:text-white transition-colors" href="/terms">Terms of Service</a>
              <a className="hover:text-white transition-colors" href="/cookies">Cookie Policy</a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}
