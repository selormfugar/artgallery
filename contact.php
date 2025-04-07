<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/head.php'; ?>

<body class="bg-gray-50">
    <?php require_once 'includes/navbar.php'; ?>

    <!-- Hero Section with Parallax Effect -->
    <section class="relative h-96 flex items-center justify-center bg-fixed bg-center bg-cover" style="background-image: url('images/contact-hero.jpg');">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 text-center px-4">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-4">Get In Touch</h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">We'd love to hear from you. Reach out for inquiries, feedback, or to plan your visit.</p>
        </div>
    </section>

    <!-- Contact Information Cards -->
    <section class="py-16 px-4 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Contact Information</h2>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Our team is available to assist you with any questions about exhibitions, events, or museum services.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                    <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Visit Us</h3>
                    <p class="text-gray-600">673 12 Constitution Lane<br>Massillon, NY 10002</p>
                </div>
                
                <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                    <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Call Us</h3>
                    <p class="text-gray-600">General Inquiries: (781) 562-9355<br>Group Bookings: (781) 727-6090</p>
                </div>
                
                <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                    <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Email Us</h3>
                    <p class="text-gray-600">General: musea@museum.org<br>Press: press@museum.org</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Map & Contact Form Section -->
    <section class="py-16 px-4 bg-gray-100">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Interactive Map -->
                <div class="h-full">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Location</h3>
                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                            <iframe 
                                class="w-full h-96"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.370462774197!2d-74.0025413845961!3d40.73083637932879!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259af5ea1575f%3A0x5aefdf8b2d16d0e6!2sWhitney%20Museum%20of%20American%20Art!5e0!3m2!1sen!2sus!4v1615508659261!5m2!1sen!2sus"
                                frameborder="0"
                                allowfullscreen=""
                                aria-hidden="false"
                                tabindex="0">
                            </iframe>
                        </div>
                        <div class="mt-6 flex flex-wrap gap-4">
                            <a href="#" class="flex items-center text-gray-700 hover:text-amber-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                Get Directions
                            </a>
                            <a href="#" class="flex items-center text-gray-700 hover:text-amber-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Parking Information
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="bg-white p-8 rounded-xl shadow-sm">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Send Us a Message</h3>
                    <p class="text-gray-600 mb-6">We typically respond within 24-48 hours.</p>
                    
                    <form action="#" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input type="text" id="name" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <select id="subject" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Select a topic</option>
                                <option value="general">General Inquiry</option>
                                <option value="group">Group Visit</option>
                                <option value="press">Press Inquiry</option>
                                <option value="education">Education Programs</option>
                                <option value="events">Private Events</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea id="message" name="message" rows="5" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full md:w-auto px-8 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg transition duration-300">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Additional Information -->
    <section class="py-16 px-4 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Hours -->
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Hours</h3>
                    <ul class="space-y-3">
                        <li class="flex justify-between">
                            <span class="text-gray-600">Tuesday - Thursday</span>
                            <span class="font-medium">9:00 AM - 7:00 PM</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Friday - Saturday</span>
                            <span class="font-medium">9:00 AM - 5:00 PM</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Sunday</span>
                            <span class="font-medium">8:00 AM - 6:00 PM</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Monday</span>
                            <span class="font-medium text-red-500">Closed</span>
                        </li>
                    </ul>
                    <p class="mt-4 text-sm text-gray-500">* Last admission 30 minutes before closing</p>
                </div>
                
                <!-- Accessibility -->
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Accessibility</h3>
                    <p class="text-gray-600 mb-4">Our museum is fully accessible to visitors with disabilities. We offer:</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-amber-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Wheelchair access throughout
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-amber-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Elevators to all floors
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-amber-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Accessible restrooms
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-amber-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sensory-friendly kits available
                        </li>
                    </ul>
                </div>
                
                <!-- Social Media -->
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Connect With Us</h3>
                    <p class="text-gray-600 mb-6">Follow us for the latest updates, virtual tours, and behind-the-scenes content.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-12 h-12 rounded-full bg-gray-100 hover:bg-amber-100 flex items-center justify-center transition duration-300">
                            <i class="fab fa-facebook-f text-gray-700 hover:text-amber-600"></i>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-full bg-gray-100 hover:bg-amber-100 flex items-center justify-center transition duration-300">
                            <i class="fab fa-instagram text-gray-700 hover:text-amber-600"></i>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-full bg-gray-100 hover:bg-amber-100 flex items-center justify-center transition duration-300">
                            <i class="fab fa-twitter text-gray-700 hover:text-amber-600"></i>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-full bg-gray-100 hover:bg-amber-100 flex items-center justify-center transition duration-300">
                            <i class="fab fa-youtube text-gray-700 hover:text-amber-600"></i>
                        </a>
                    </div>
                    
                    <div class="mt-8">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Newsletter</h4>
                        <p class="text-gray-600 mb-4">Subscribe for exhibition updates and special events.</p>
                        <form class="flex">
                            <input type="email" placeholder="Your email" class="flex-grow px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-r-lg transition duration-300">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require_once 'includes/footer.php'; ?>

    <script>
        // Simple form validation example
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
        
   
  // Check for saved theme preference or use system preference
  if (localStorage.getItem('theme') === 'dark' || 
      (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark-mode');
    document.getElementById('light-icon').classList.add('hidden');
    document.getElementById('dark-icon').classList.remove('hidden');
  }

  function toggleDarkMode() {
    const html = document.documentElement;
    html.classList.toggle('dark-mode');
    
    const isDark = html.classList.contains('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    
    document.getElementById('light-icon').classList.toggle('hidden');
    document.getElementById('dark-icon').classList.toggle('hidden');
  }
</script>
</body>
</html>