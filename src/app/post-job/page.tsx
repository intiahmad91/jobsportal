export default function PostJobPage() {
  return (
    <main>
      {/* Header Start */}
      <div className="headerWrp">
        <div className="container">
          <div className="headermenu">
            <nav className="navbar navbar-expand-lg">
              <a className="navbar-brand headerlogo" href="/">
                <img src="/images/full-timez-logo.png" alt="" />
              </a>

              <form>
                <div className="row">
                  <div className="col-lg-3">
                    <input type="search" className="search-input form-control" placeholder="Job Title" />
                  </div>
                  <div className="col-lg-3">
                    <input type="search" className="search-input form-control" placeholder="Location" />
                  </div>
                  <div className="col-lg-3">
                    <input type="search" className="search-input form-control" placeholder="Specialist" />
                  </div>
                  <div className="col-lg-3">
                    <div className="d-flex">
                      <button className="submit_btn">Search</button>
                      <div className="login"><a href="/login">Login</a></div>
                    </div>
                  </div>
                </div>
              </form>
            </nav>

            <nav className="navbar navbar-light menubtn">
              <button className="menu-toggle" onClick={() => {
                const sidebar = document.getElementById("sidebarMenu");
                if (sidebar) sidebar.classList.toggle("show");
              }}>☰</button>
            </nav>
          </div>

          <div id="sidebarMenu" className="offcanvas-menu">
            <div className="menu-header">
              <h5>Navigation</h5>
              <button onClick={() => {
                const sidebar = document.getElementById("sidebarMenu");
                if (sidebar) sidebar.classList.toggle("show");
              }} className="btn-close"></button>
            </div>
            <nav>
              <a href="/" className="nav-link">Home</a>
              <a href="/jobs" className="nav-link">Jobseeker</a>
              <a href="/jobs" className="nav-link">Jobs</a>
              <a href="/job-single" className="nav-link">Job Single</a>
              <a href="/post-job" className="nav-link">Post Job</a>
              <a href="/candidates" className="nav-link">Candidate Listing</a>
              <a href="/candidate-details" className="nav-link">Candidate Details</a>
              <a href="#." className="nav-link">Admin</a>
              <a href="/dashboard" className="nav-link">Dashboard</a>
              <a href="/my-profile" className="nav-link">My Profile</a>
              <a href="/login" className="nav-link">Login</a>
              <a href="/change-password" className="nav-link">Change Password</a>
              <a href="/contact" className="nav-link">Contact Us</a>
            </nav>
          </div>
        </div>
      </div>

      {/* Hero Section */}
      <div className="container-fluid position-relative">
        <section className="hero-section hero">
          <h1 className="text-center">FULLTIMEZ</h1>
          <div className="hero-content text-center">
            <div className="bannerinfo">
              <p className="caption"> <span>Make the best move to choose your new job</span> </p>
              <div className="dropdown">
                <button className="btn btn-secondary dropdown-toggle cta" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                  Worldwide Registered Consultant
                </button>
                <ul className="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                  <li><a className="dropdown-item" href="/canada">CANADA</a></li>
                  <li><a className="dropdown-item" href="/australia">AUSTRALIA</a></li>
                  <li><a className="dropdown-item disabled" href="/newzealand">NEW ZEALAND</a></li>
                </ul>
              </div>
            </div>
            <div className="women"><img src="/images/women.png" alt="" /></div>
          </div>
        </section>
      </div>

      {/* Breadcrumb Section */}
      <section className="breadcrumb-section">
        <div className="container-auto">
          <div className="row">
            <div className="col-md-6 col-sm-6 col-12">
              <div className="page-title">
                <h1>Post Job</h1>
              </div>
            </div>
            <div className="col-md-6 col-sm-6 col-12">
              <nav aria-label="breadcrumb" className="theme-breadcrumb">
                <ol className="breadcrumb">
                  <li className="breadcrumb-item"><a href="/">Home</a></li>
                  <li className="breadcrumb-item active" aria-current="page">Post Job</li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </section>

      {/* Job Form Section */}
      <section className="job_forms">
        <div className="container">
          <div className="form-container">
            <h2>Post a Job</h2>
            <form className="job-form">
              <label htmlFor="title">Job Title</label>
              <input type="text" id="title" name="title" placeholder="e.g., Software Engineer" required />

              <label htmlFor="company">Company Name</label>
              <input type="text" id="company" name="company" placeholder="e.g., Tech Inc." required />

              <label htmlFor="description">Job Description</label>
              <textarea id="description" name="description" rows={5} placeholder="Describe the job in detail..." required></textarea>

              <label htmlFor="location">Location</label>
              <input type="text" id="location" name="location" placeholder="e.g., Dubai, Abu Dhabi" required />

              <label htmlFor="type">Job Type</label>
              <select id="type" name="type" required>
                <option value="">Select Type</option>
                <option value="full-time">Full-time</option>
                <option value="part-time">Part-time</option>
                <option value="contract">Contract</option>
                <option value="internship">Internship</option>
              </select>

              <label htmlFor="salary">Salary</label>
              <input type="text" id="salary" name="salary" placeholder="e.g., 6000 AED/month" />

              <button type="submit" className="submit-btn">Post Job</button>
            </form>
          </div>
        </div>
      </section>

      {/* Download App Section */}
      <section className="section download">
        <div className="container">
          <div className="row">
            <div className="col-md-6 col-12 order-md-1 order-2">
              <div className="section__header">
                <h2 className="section__title">Download <span>FULLTIMEZ's</span> <br className="d-none d-md-block" /> mobile app for Free</h2>
                <p className="section__desc">
                  Use Daftra mobile application and access your business anywhere, anytime, from any device.
                  Daftra mobile is as easy to manage with a seamless and user-friendly interface, stay
                  connected to your business, register your recent transactions, update your data on the go
                  and constantly keep track of your business and team's performance.
                </p>
                <div className="download__actions">
                  <a href="#">
                    <img title="e-Invoice KSA iOS mobile APP" src="/images/apple.webp" className="lazyload" alt="apple mobile accounting APP" />
                  </a>
                  <a href="#">
                    <img title="e-Invoice KSA andriod mobile APP" alt="andriod mobile accounting APP" src="/images/google-play.jpg" className="lazyload" />
                  </a>
                  <a href="#" style={{ marginTop: '5px' }}>
                    <img title="e-Invoice KSA Huawei mobile APP" alt="Mobile Accounting APP Huawei" src="/images/huawei.webp" className="lazyload" />
                  </a>
                </div>
              </div>
            </div>
            <div className="col-md-6 col-12 order-md-2 order-1 bg-layer">
              <div className="section-image text-center">
                <img src="/images/download-img.webp" className="w-100 lazyload" alt="" />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="section desktop-footer">
        <div className="container">
          <div className="row">
            <div className="col-md-4 col-sm-6 col-12">
              <div className="footer__item">
                <p className="special_p">Modules</p>
                <p>
                  <a href="/en/sales"><span>Sales management software </span></a>
                  <a href="/en/inventory"><span>Inventory management software </span></a>
                  <a href="/en/finance-accounting"><span>Accounting software </span></a>
                  <a href="/en/work-orders"><span>Operations management software </span></a>
                  <a href="/en/crm"><span>Clients relationship management software </span></a>
                  <a href="#"><span>Human resources management software </span></a>
                  <a className="logo d-none d-md-block mt-5" href="/">
                    <img src="/images/full-timez-logo-white.png" alt="" loading="lazy" />&nbsp;
                  </a>
                </p>
                <div className="social-icons" style={{ display: 'flex', gap: '20px', margin: '40px 0' }}>
                  &nbsp;
                </div>
              </div>
            </div>
            <div className="col-md-4 col-sm-6 col-12">
              <div className="footer__item">
                <p className="special_p">Sitemap</p>
                <p>
                  <a href="#"><span>Sign Up </span></a>
                  <a href="#"><span>Login </span></a>
                  <a href="#"><span>Prices </span></a>
                  <a href="#"><span>Contact Us </span></a>
                  <a href="#"><span>Profit and Partnership Program </span></a>
                  <a href="#"><span>Success Partners </span></a>
                  <a href="#"><span>Authorized Suppliers </span></a>
                  <a href="#"><span>Agency program&nbsp;</span></a>
                  <a href="#">System updates</a>
                  <a href="#">API Docs</a>
                </p>
                <div className="footer__item mobile_app" style={{ marginTop: '20px' }}>
                  <p className="special_p">Mobile App</p>
                  <p>
                    <a className="mobile_app_icon apple_store" style={{ color: '#000000' }} href="#" target="_blank">
                      <span><img className="image_resized" style={{ width: '30%' }} src="/images/apple_app_store.svg" alt="" title="Apple Store" loading="lazy" /> </span>
                    </a>
                    <a className="mobile_app_icon play_store" style={{ color: '#000000' }} href="#" target="_blank">
                      <span><img className="image_resized" style={{ width: '30%' }} src="/images/google_play_store.svg" alt="google-play-store-android" title="Google Play Store" loading="lazy" /> </span>
                    </a>
                    <a className="mobile_app_icon huawei_store" style={{ color: '#000000' }} href="#" target="_blank">
                      <span><img className="image_resized" style={{ width: '30%' }} src="/images/huawei_app_gallery.svg" alt="huawei-app-gallery-android" title="Huawei App Gallery" loading="lazy" />&nbsp;</span>
                    </a>
                  </p>
                </div>
              </div>
            </div>
            <div className="col-md-4 col-sm-6 col-12">
              <div className="footer__item">
                <p className="special_p">Industries</p>
                <p>
                  <a href="#"><span>Construction and real estate investment </span></a>
                  <a href="#"><span>Maintenance center and workshop </span></a>
                  <a href="#"><span>Translation center management </span></a>
                  <a href="#"><span>Legal offices, advisory and advocacy management </span></a>
                  <a href="#"><span>Program for managing educational centers </span></a>
                  <a href="#"><span>School and nursery management program </span></a>
                  <a href="#"><span>Gym, fitness centers and health clubs </span></a>
                  <a href="#"><span>Clinics and medical centers management </span></a>
                </p>
              </div>
            </div>
          </div>
          <div className="footer-bottom">
            <div className="copyright">
              ©2025 Full Timez. All rights reserved.
            </div>
            <div className="terms">
              <a href="#">Terms and Conditions </a>|
              <a href="#">Privacy Policy</a>
            </div>
          </div>
        </div>
      </footer>
    </main>
  )
}
