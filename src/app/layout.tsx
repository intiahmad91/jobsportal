import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import './globals.css'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'FullTimez',
  description: 'Job Portal',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="shortcut icon" href="/favicon.ico" />
        
        {/* Bootstrap CSS */}
        <link href="/css/bootstrap.min.css" rel="stylesheet" />
        <link href="/css/all.css" rel="stylesheet" />
        <link href="/css/fontawesome.css" rel="stylesheet" />
        <link href="/css/owl.carousel.css" rel="stylesheet" />
        <link href="/css/animate.css" rel="stylesheet" />
        <link href="/css/magnific-popup.css" rel="stylesheet" />
        <link href="/css/newfancybox.css" rel="stylesheet" />
        <link href="/css/style.css" rel="stylesheet" />
      </head>
      <body className={inter.className}>
        {children}
        
        {/* JavaScript Files */}
        <script src="/js/bootstrap.bundle.min.js"></script>
        <script src="/js/jquery.min.js"></script>
        <script src="/js/jquery.fancybox.min.js"></script>
        <script src="/js/jquery.magnific-popup.min.js"></script>
        <script src="/js/animate.js"></script>
        <script src="/js/wow.js"></script>
        <script src="/js/owl.carousel.js"></script>
        <script src="/js/script.js"></script>
        
        <script dangerouslySetInnerHTML={{
          __html: `
            new WOW().init();
            
            function toggleMenu() {
              document.getElementById("sidebarMenu").classList.toggle("show");
            }
          `
        }} />
      </body>
    </html>
  )
}
