<?php
session_start();
// After successful login in your login.php:
if (isset($_SESSION['user_id'])) {
    echo '<script>checkPendingWishlistItem();</script>';
    
    // Or if you're redirecting back:
    if (isset($_GET['redirect'])) {
        header("Location: " . urldecode($_GET['redirect']));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/head.php'; ?>

<body>
<?php require_once 'includes/navbar.php'; ?>

    <!-- Hero Section with Parallax Effect -->
    <section class="hero-section bg-fixed bg-center bg-cover relative h-screen flex items-center justify-center">
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="hero-content text-center relative z-10 px-4">
            <h1 class="text-6xl md:text-8xl font-bold mb-6 text-white">Our Story</h1>
            <p class="text-xl md:text-2xl text-gray-300 max-w-3xl mx-auto">Discover the passion behind our collections and our commitment to preserving artistic heritage</p>
        </div>
        <div class="absolute bottom-10 left-0 right-0 flex justify-center">
            <a href="#about" class="animate-bounce">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </a>
        </div>
    </section>

    <!-- About The Museum -->
    <section id="about" class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-lg font-medium text-amber-400 tracking-widest">OUR LEGACY</span>
                <h2 class="text-4xl md:text-5xl font-bold text-white mt-4">The Museum Experience</h2>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <p class="text-gray-300 text-lg leading-relaxed">
                        Founded in 1965, our museum has been a beacon of artistic expression and cultural preservation. 
                        What began as a small private collection has blossomed into one of the region's most visited 
                        cultural institutions.
                    </p>
                    <p class="text-gray-300 text-lg leading-relaxed">
                        Our mission extends beyond exhibition - we're committed to education, conservation, and making 
                        art accessible to all. Each year, we welcome over 500,000 visitors and host more than 50 
                        special exhibitions.
                    </p>
                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <div class="bg-gray-800 p-6 rounded-lg">
                            <h3 class="text-amber-400 text-4xl font-bold">10+</h3>
                            <p class="text-gray-300 mt-2">Annual Exhibitions</p>
                        </div>
                        <div class="bg-gray-800 p-6 rounded-lg">
                            <h3 class="text-amber-400 text-4xl font-bold">500+</h3>
                            <p class="text-gray-300 mt-2">Yearly Visitors</p>
                        </div>
                    </div>
                </div>
                <div class="relative h-96 rounded-xl overflow-hidden">
                    <img src="images/museum-building.jpg" alt="Museum exterior" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex items-end p-8">
                        <p class="text-white text-lg">Our iconic building, designed by renowned architect Maria Vasquez</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Values -->
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-lg font-medium text-amber-400 tracking-widest">OUR COMMITMENT</span>
                <h2 class="text-4xl md:text-5xl font-bold text-white mt-4">Mission & Values</h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gray-900 p-8 rounded-lg hover:transform hover:scale-105 transition duration-300">
                    <div class="w-14 h-14 bg-amber-400 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Inspire Creativity</h3>
                    <p class="text-gray-400">We believe art has the power to transform lives and spark imagination in people of all ages.</p>
                </div>
                
                <div class="bg-gray-900 p-8 rounded-lg hover:transform hover:scale-105 transition duration-300">
                    <div class="w-14 h-14 bg-amber-400 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Preserve Heritage</h3>
                    <p class="text-gray-400">Our conservation team works tirelessly to protect artistic treasures for future generations.</p>
                </div>
                
                <div class="bg-gray-900 p-8 rounded-lg hover:transform hover:scale-105 transition duration-300">
                    <div class="w-14 h-14 bg-amber-400 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Educate</h3>
                    <p class="text-gray-400">Through workshops, tours and digital content, we make art education accessible to all.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Gallery with Interactive Grid -->
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-lg font-medium text-amber-400 tracking-widest">EXPLORE</span>
                <h2 class="text-4xl md:text-5xl font-bold text-white mt-4">Gallery Highlights</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="gallery-item relative group overflow-hidden rounded-lg h-80">
                    <img src="images/art1.webp" alt="Gallery 1" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 p-6">
                        <p class="text-white text-center text-lg">"Starry Night" by Vincent Van Gogh (1889)</p>
                    </div>
                </div>
                <div class="gallery-item relative group overflow-hidden rounded-lg h-80">
                    <img src="images/art7.jpg" alt="Gallery 2" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 p-6">
                        <p class="text-white text-center text-lg">Contemporary sculpture from our modern art wing</p>
                    </div>
                </div>
                <div class="gallery-item relative group overflow-hidden rounded-lg h-80">
                    <img src="images/art21.jpg" alt="Gallery 3" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 p-6">
                        <p class="text-white text-center text-lg">Ancient artifacts from our archaeological collection</p>
                    </div>
                </div>
                <div class="gallery-item relative group overflow-hidden rounded-lg h-80">
                    <img src="images/art22.jpg" alt="Gallery 4" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 p-6">
                        <p class="text-white text-center text-lg">Interactive digital art installation</p>
                    </div>
                </div>
                <div class="gallery-item relative group overflow-hidden rounded-lg h-80">
                    <img src="images/art25.jpg" alt="Gallery 5" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 p-6">
                        <p class="text-white text-center text-lg">Renaissance masterpieces gallery</p>
                    </div>
                </div>
                <div class="gallery-item relative group overflow-hidden rounded-lg h-80">
                    <img src="images/art26.jpg" alt="Gallery 6" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 p-6">
                        <p class="text-white text-center text-lg">Our popular children's art discovery zone</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Visitor Information -->
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-white mb-8">Plan Your Visit</h2>
                    
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-xl font-bold text-amber-400 mb-3">Hours</h3>
                            <ul class="space-y-2 text-gray-300">
                                <li class="flex justify-between max-w-xs">
                                    <span>Tuesday - Thursday</span>
                                    <span>9:00 AM - 7:00 PM</span>
                                </li>
                                <li class="flex justify-between max-w-xs">
                                    <span>Friday - Saturday</span>
                                    <span>9:00 AM - 5:00 PM</span>
                                </li>
                                <li class="flex justify-between max-w-xs">
                                    <span>Sunday</span>
                                    <span>8:00 AM - 6:00 PM</span>
                                </li>
                                <li class="flex justify-between max-w-xs">
                                    <span>Monday</span>
                                    <span>Closed</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-amber-400 mb-3">Admission</h3>
                            <ul class="space-y-2 text-gray-300">
                                <li class="flex justify-between max-w-xs">
                                    <span>Adults</span>
                                    <span>$25</span>
                                </li>
                                <li class="flex justify-between max-w-xs">
                                    <span>Seniors (65+)</span>
                                    <span>$18</span>
                                </li>
                                <li class="flex justify-between max-w-xs">
                                    <span>Students</span>
                                    <span>$12</span>
                                </li>
                                <li class="flex justify-between max-w-xs">
                                    <span>Children under 12</span>
                                    <span>Free</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-amber-400 mb-3">Accessibility</h3>
                            <p class="text-gray-300">Our museum is fully wheelchair accessible. We offer sensory-friendly hours on the first Sunday of each month from 8-10 AM.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-900 p-8 rounded-xl">
                    <h3 class="text-2xl font-bold text-white mb-6">Special Exhibitions</h3>
                    
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-24 h-24 bg-gray-700 rounded-lg overflow-hidden">
                                <img src="images/exhibition-1.jpg" alt="Exhibition 1" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">Modern Masters</h4>
                                <p class="text-gray-400 text-sm">Through October 15, 2023</p>
                                <p class="text-gray-300 mt-2 text-sm">Explore groundbreaking works from contemporary artists pushing boundaries.</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-24 h-24 bg-gray-700 rounded-lg overflow-hidden">
                                <img src="images/exhibition-2.jpg" alt="Exhibition 2" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">Ancient Civilizations</h4>
                                <p class="text-gray-400 text-sm">November 5, 2023 - January 20, 2024</p>
                                <p class="text-gray-300 mt-2 text-sm">Artifacts from Mesopotamia, Egypt, and the Indus Valley.</p>
                            </div>
                        </div>
                    </div>
                    
                    <button class="mt-8 w-full py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-medium rounded-lg transition duration-300">
                        Book Tickets
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-lg font-medium text-amber-400 tracking-widest">VISITOR VOICES</span>
                <h2 class="text-4xl md:text-5xl font-bold text-white mt-4">What People Say</h2>
            </div>
            
            <div class="relative">
                <div class="testimonial-carousel flex overflow-hidden">
                    <div class="testimonial-slide min-w-full px-4">
                        <div class="bg-gray-800 p-8 rounded-xl">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 rounded-full overflow-hidden mr-4">
                                    <img src="images/visitor1.jpg" alt="James Carter" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">James Carter</h3>
                                    <p class="text-amber-400">Art Enthusiast</p>
                                </div>
                            </div>
                            <p class="text-gray-300 italic text-lg">"The Modern Masters exhibition was breathtaking. The curation provided such depth and context to each piece. I've visited three times already!"</p>
                            <div class="flex mt-6">
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-slide min-w-full px-4">
                        <div class="bg-gray-800 p-8 rounded-xl">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 rounded-full overflow-hidden mr-4">
                                    <img src="images/visitor2.jpg" alt="Sophia Lee" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Sophia Lee</h3>
                                    <p class="text-amber-400">Art Collector</p>
                                </div>
                            </div>
                            <p class="text-gray-300 italic text-lg">"Bringing my students here was transformative. The educational programs are perfectly tailored for different age groups. The kids haven't stopped talking about it!"</p>
                            <div class="flex mt-6">
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center mt-8 space-x-2">
                    <button onclick="prevTestimonial()" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-amber-400 flex items-center justify-center transition duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button onclick="nextTestimonial()" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-amber-400 flex items-center justify-center transition duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-lg font-medium text-amber-400 tracking-widest">BEHIND THE SCENES</span>
                <h2 class="text-4xl md:text-5xl font-bold text-white mt-4">Art Conservation</h2>
                <p class="text-gray-400 max-w-3xl mx-auto mt-4">Discover how our team preserves priceless artworks for future generations</p>
            </div>
            
            <div class="aspect-w-16 aspect-h-9 bg-gray-900 rounded-xl overflow-hidden">
                <iframe class="w-full h-full" src="https://player.vimeo.com/video/204646310" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
            </div>
        </div>
    </section>

    <!-- Location Section -->
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-white mb-6">Find Us</h2>
                    <p class="text-gray-300 mb-8">Located in the heart of the cultural district, our museum is easily accessible by public transportation and offers ample parking for visitors.</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-amber-400 mt-1 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <h3 class="text-xl font-bold text-white">Address</h3>
                                <p class="text-gray-400">123 Art Avenue, Cultural District<br>New York, NY 10001</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-amber-400 mt-1 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <div>
                                <h3 class="text-xl font-bold text-white">Contact</h3>
                                <p class="text-gray-400">info@museumname.org<br>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-amber-400 mt-1 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <h3 class="text-xl font-bold text-white">Special Events</h3>
                                <p class="text-gray-400">We host private tours, corporate events, and weddings. Contact our events team for details.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="h-96 rounded-xl overflow-hidden shadow-xl">
                    <iframe class="w-full h-full" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.370462774197!2d-74.0025413845961!3d40.73083637932879!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259af5ea1575f%3A0x5aefdf8b2d16d0e6!2sWhitney%20Museum%20of%20American%20Art!5e0!3m2!1sen!2sus!4v1615508659261!5m2!1sen!2sus" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Stay Connected</h2>
            <p class="text-gray-400 mb-8 max-w-2xl mx-auto">Subscribe to our newsletter for exhibition updates, special events, and exclusive content.</p>
            
            <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input type="email" placeholder="Your email address" class="flex-grow px-4 py-3 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                <button type="submit" class="px-6 py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-medium rounded-lg transition duration-300">Subscribe</button>
            </form>
        </div>
    </section>

    <script>
        let currentTestimonial = 0;
        const testimonials = document.querySelectorAll('.testimonial-slide');
        const totalTestimonials = testimonials.length;
        
        function showTestimonial(index) {
            testimonials.forEach((testimonial, i) => {
                testimonial.style.transform = `translateX(-${index * 100}%)`;
            });
        }
        
        function nextTestimonial() {
            currentTestimonial = (currentTestimonial + 1) % totalTestimonials;
            showTestimonial(currentTestimonial);
        }
        
        function prevTestimonial() {
            currentTestimonial = (currentTestimonial - 1 + totalTestimonials) % totalTestimonials;
            showTestimonial(currentTestimonial);
        }
        
        // Auto-rotate testimonials
        setInterval(nextTestimonial, 8000);
      
      
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