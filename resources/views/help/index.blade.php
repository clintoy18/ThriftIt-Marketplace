<x-app-layout>
    <div class="py-12 bg-white dark:bg-gray-700 dark:text-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                    Help Center
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Find answers to common questions and learn how to use ThriftIT
                </p>
            </div>

            <!-- Search Bar -->
            <div class="mb-8">
                <div class="relative max-w-2xl mx-auto">
                    <input type="text" placeholder="Search for help..." 
                        class="w-full px-6 py-4 pl-12 rounded-full border-2 border-gray-300 dark:border-gray-600 focus:border-[#B59F84] focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
                <a href="#getting-started" 
                    class="bg-[#F4F2ED] dark:bg-gray-800 p-6 rounded-xl hover:shadow-lg transition-shadow duration-300 text-center">
                    <div class="w-12 h-12 bg-[#B59F84] rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Getting Started</h3>
                </a>
                <a href="#buying-guide" 
                    class="bg-[#F4F2ED] dark:bg-gray-800 p-6 rounded-xl hover:shadow-lg transition-shadow duration-300 text-center">
                    <div class="w-12 h-12 bg-[#B59F84] rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Buying Guide</h3>
                </a>
                <a href="#selling-guide" 
                    class="bg-[#F4F2ED] dark:bg-gray-800 p-6 rounded-xl hover:shadow-lg transition-shadow duration-300 text-center">
                    <div class="w-12 h-12 bg-[#B59F84] rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Selling Guide</h3>
                </a>
                <a href="#donations" 
                    class="bg-[#F4F2ED] dark:bg-gray-800 p-6 rounded-xl hover:shadow-lg transition-shadow duration-300 text-center">
                    <div class="w-12 h-12 bg-[#B59F84] rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Donations</h3>
                </a>
            </div>

            <!-- Main Content -->
            <div class="space-y-12">
                <!-- Getting Started Section -->
                <section id="getting-started" class="scroll-mt-24">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-[#B59F84] rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Getting Started</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- How to create an account -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Create an Account
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Click on the <strong>"Sign Up"</strong> button in the top right corner of the page.</p>
                                    <p>2. Fill in your personal information:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li>First Name and Last Name</li>
                                        <li>Email address</li>
                                        <li>Password (must be at least 8 characters)</li>
                                        <li>Phone number</li>
                                    </ul>
                                    <p>3. Accept the Terms and Conditions.</p>
                                    <p>4. Click <strong>"Register"</strong> to create your account.</p>
                                    <p>5. Check your email for a verification link and click it to activate your account.</p>
                                </div>
                            </div>

                            <!-- How to verify your account -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Verify Your Account
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. After creating your account, check your email inbox.</p>
                                    <p>2. Look for the verification email from ThriftIT.</p>
                                    <p>3. Click the verification link in the email.</p>
                                    <p>4. You'll be redirected to the platform with your account verified.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <strong>Note:</strong> If you don't receive the email, check your spam folder or request a new verification email from the login page.
                                    </p>
                                </div>
                            </div>

                            <!-- How to edit profile -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Edit Your Profile
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Click on your name in the top right corner to open the dropdown menu.</p>
                                    <p>2. Select <strong>"Profile"</strong> from the menu.</p>
                                    <p>3. Click the <strong>"Edit Profile"</strong> button or go to Settings → Update Profile Information.</p>
                                    <p>4. Update any information you want to change:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li>Name</li>
                                        <li>Email</li>
                                        <li>Phone number</li>
                                        <li>Bio/Description</li>
                                        <li>Location</li>
                                    </ul>
                                    <p>5. Click <strong>"Save Changes"</strong> to update your profile.</p>
                                </div>
                            </div>

                            <!-- How to upload a profile picture -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Upload a Profile Picture
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Go to your Profile page.</p>
                                    <p>2. Click on the profile picture area or the <strong>"Edit Profile"</strong> button.</p>
                                    <p>3. Click on the current profile picture or the placeholder image.</p>
                                    <p>4. Select an image file from your device (JPG, PNG, or JPEG format).</p>
                                    <p>5. Crop or adjust the image if needed.</p>
                                    <p>6. Click <strong>"Save"</strong> or <strong>"Upload"</strong> to set your new profile picture.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <strong>Tip:</strong> Use a clear, well-lit photo for the best results. Recommended size: 400x400 pixels.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Buying Guide Section -->
                <section id="buying-guide" class="scroll-mt-24">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-[#B59F84] rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Buying Guide</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- How to search for items -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Search for Items
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Use the search bar at the top of the page.</p>
                                    <p>2. Type keywords related to what you're looking for (e.g., "vintage jacket", "laptop", "furniture").</p>
                                    <p>3. Press Enter or click the search icon.</p>
                                    <p>4. Use filters to narrow down results:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li>Category (Clothing, Electronics, Furniture, etc.)</li>
                                        <li>Price range</li>
                                        <li>Location/Barangay</li>
                                        <li>Condition (New, Like New, Good, Fair)</li>
                                        <li>Size</li>
                                    </ul>
                                    <p>5. Browse through the results and click on items that interest you.</p>
                                </div>
                            </div>

                            <!-- How to contact the seller -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Contact the Seller
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Go to the product page you're interested in.</p>
                                    <p>2. Scroll down to find the <strong>"Send seller a message"</strong> section.</p>
                                    <p>3. Click the <strong>"Send"</strong> button to open a chat with the seller.</p>
                                    <p>4. Alternatively, click the <strong>"Message"</strong> button on the seller's profile card.</p>
                                    <p>5. Type your message and send it. The seller will be notified and can reply to you.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <strong>Tip:</strong> Ask specific questions about the item's condition, size, or availability before making a purchase.
                                    </p>
                                </div>
                            </div>

                            <!-- How to check item condition -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Check Item Condition
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. On the product page, look for the <strong>"Condition"</strong> badge in the product details.</p>
                                    <p>2. Read the product description carefully for any specific condition notes.</p>
                                    <p>3. Review all product photos to see the actual condition of the item.</p>
                                    <p>4. Contact the seller if you need more details or additional photos.</p>
                                    <p>5. Condition categories include:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li><strong>New:</strong> Unused, in original packaging</li>
                                        <li><strong>Like New:</strong> Gently used, excellent condition</li>
                                        <li><strong>Good:</strong> Used but well-maintained</li>
                                        <li><strong>Fair:</strong> Shows signs of wear but functional</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- How to buy items safely -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Buy Items Safely
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. <strong>Verify the seller:</strong> Check their profile, ratings, and reviews from other buyers.</p>
                                    <p>2. <strong>Read the description:</strong> Make sure you understand what you're buying.</p>
                                    <p>3. <strong>Check the photos:</strong> Look at all product images carefully.</p>
                                    <p>4. <strong>Ask questions:</strong> Contact the seller if you have any doubts.</p>
                                    <p>5. <strong>Review payment method:</strong> The seller should provide a QR code for payment.</p>
                                    <p>6. <strong>Upload payment proof:</strong> After payment, upload a screenshot of your payment confirmation.</p>
                                    <p>7. <strong>Wait for confirmation:</strong> The seller will confirm your order after verifying payment.</p>
                                    <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-2 font-semibold">
                                        <strong>⚠️ Safety Tip:</strong> Never share personal information like passwords or bank account details. Only communicate through the platform's messaging system.
                                    </p>
                                </div>
                            </div>

                            <!-- How to track your order -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Track Your Order
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Go to your Profile page.</p>
                                    <p>2. Click on the <strong>"Orders"</strong> tab.</p>
                                    <p>3. Find your order in the list and click on it to view details.</p>
                                    <p>4. You'll see the order status:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li><strong>Pending:</strong> Payment proof submitted, waiting for seller confirmation</li>
                                        <li><strong>Approved:</strong> Seller confirmed your payment</li>
                                        <li><strong>Delivering:</strong> Item is on the way</li>
                                        <li><strong>Completed:</strong> Order delivered and completed</li>
                                        <li><strong>Cancelled:</strong> Order was cancelled</li>
                                    </ul>
                                    <p>5. Contact the seller through messages if you need updates on delivery.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Selling Guide Section -->
                <section id="selling-guide" class="scroll-mt-24">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-[#B59F84] rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Selling Guide</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- How to list an item -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to List an Item (Sell)
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Click on <strong>"Sell"</strong> in the navigation menu.</p>
                                    <p>2. Fill in the product information:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li>Product name</li>
                                        <li>Description (be detailed and honest)</li>
                                        <li>Category</li>
                                        <li>Condition</li>
                                        <li>Size (if applicable)</li>
                                        <li>Quantity</li>
                                        <li>Location (Barangay)</li>
                                    </ul>
                                    <p>3. Upload product photos (at least one, up to multiple images).</p>
                                    <p>4. Set your price or choose "For Donation".</p>
                                    <p>5. (Optional) Upload a payment QR code if selling for money.</p>
                                    <p>6. Review all information and click <strong>"Publish"</strong>.</p>
                                    <p>7. Wait for admin approval. You'll be notified once your listing is approved.</p>
                                </div>
                            </div>

                            <!-- How to set price -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Set Price
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. When listing an item, you'll see a price field.</p>
                                    <p>2. Enter the amount in Philippine Peso (₱).</p>
                                    <p>3. Consider these factors when pricing:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li>Original purchase price</li>
                                        <li>Current condition</li>
                                        <li>Age and usage</li>
                                        <li>Market value of similar items</li>
                                        <li>Demand for the item</li>
                                    </ul>
                                    <p>4. You can also choose <strong>"For Donation"</strong> if you want to give the item away for free.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <strong>Tip:</strong> Research similar items on the platform to set a competitive price.
                                    </p>
                                </div>
                            </div>

                            <!-- How to upload item photos -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Upload Item Photos
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. On the listing form, find the photo upload section.</p>
                                    <p>2. Click <strong>"Upload Images"</strong> or drag and drop photos.</p>
                                    <p>3. Select multiple photos from your device (JPG, PNG, or JPEG).</p>
                                    <p>4. Tips for great product photos:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li>Use good lighting (natural light is best)</li>
                                        <li>Take photos from different angles</li>
                                        <li>Show any defects or wear clearly</li>
                                        <li>Include close-ups of important details</li>
                                        <li>Use a clean, uncluttered background</li>
                                    </ul>
                                    <p>5. Rearrange photos by dragging (the first photo will be the main image).</p>
                                    <p>6. Remove photos by clicking the X button if needed.</p>
                                </div>
                            </div>

                            <!-- How to handle negotiations -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Handle Negotiations
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Buyers may message you to negotiate the price.</p>
                                    <p>2. Respond promptly and professionally through the messaging system.</p>
                                    <p>3. Consider the offer and decide if you're willing to accept it.</p>
                                    <p>4. If you agree to a new price:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li>You can update the product price on the product page</li>
                                        <li>Or inform the buyer that you'll update it</li>
                                        <li>Make sure both parties agree before proceeding</li>
                                    </ul>
                                    <p>5. Be polite and clear in your communication.</p>
                                    <p>6. If you're not open to negotiations, politely let the buyer know.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <strong>Note:</strong> All negotiations should happen through the platform's messaging system for your safety and record-keeping.
                                    </p>
                                </div>
                            </div>

                            <!-- How to mark item as sold -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Mark Item as Sold
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Go to your product page.</p>
                                    <p>2. Scroll to the product actions section.</p>
                                    <p>3. Click the <strong>"Mark as Sold"</strong> button.</p>
                                    <p>4. Confirm the action when prompted.</p>
                                    <p>5. The product status will change to "Sold" and will no longer appear in search results.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <strong>Note:</strong> You can only mark items as sold if they have been approved by admin and are currently available. If you've received an order, the status will automatically update when you approve the buyer's payment.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Donations Section -->
                <section id="donations" class="scroll-mt-24">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-[#B59F84] rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Donating Items</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- How to list an item for donation -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to List an Item for Donation
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Go to the <strong>"Sell"</strong> page to create a new listing.</p>
                                    <p>2. Fill in all the required product information (name, description, category, condition, etc.).</p>
                                    <p>3. In the listing type section, select <strong>"For Donation"</strong> instead of setting a price.</p>
                                    <p>4. Upload photos of the item you're donating.</p>
                                    <p>5. Set your location (Barangay).</p>
                                    <p>6. Review the information and click <strong>"Publish"</strong>.</p>
                                    <p>7. Your donation listing will be submitted for admin approval.</p>
                                    <p>8. Once approved, it will appear in the <strong>"Donation Hub"</strong> for others to see.</p>
                                </div>
                            </div>

                            <!-- Rules for donated items -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    Rules for Donated Items
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>To maintain a positive community experience, please follow these rules:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li><strong>Quality Standards:</strong> Items should be in usable condition. Avoid donating broken, damaged, or unsanitary items.</li>
                                        <li><strong>Honest Descriptions:</strong> Accurately describe the condition and any defects of donated items.</li>
                                        <li><strong>No Prohibited Items:</strong> Do not donate illegal, dangerous, or restricted items.</li>
                                        <li><strong>Complete Information:</strong> Provide clear photos and detailed descriptions.</li>
                                        <li><strong>Follow Through:</strong> If someone requests your donation, be responsive and follow through with the donation.</li>
                                        <li><strong>One Item Per Listing:</strong> Create separate listings for different items.</li>
                                    </ul>
                                    <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-2 font-semibold">
                                        <strong>⚠️ Important:</strong> Violation of these rules may result in your donation listing being removed or your account being restricted.
                                    </p>
                                </div>
                            </div>

                            <!-- How donors and recipients communicate -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How Donors and Recipients Communicate
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. <strong>Recipients can express interest:</strong> People interested in your donation can message you through the platform.</p>
                                    <p>2. <strong>Review requests:</strong> Check messages from potential recipients and review their profiles if needed.</p>
                                    <p>3. <strong>Coordinate pickup/delivery:</strong> Discuss how the item will be transferred:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li>Meet in a public place for safety</li>
                                        <li>Arrange a pickup time and location</li>
                                        <li>Or coordinate delivery if both parties agree</li>
                                    </ul>
                                    <p>4. <strong>Confirm the donation:</strong> Once the item is received, the recipient can confirm it in the system.</p>
                                    <p>5. <strong>Update listing:</strong> Mark the donation as completed or remove the listing once it's been given away.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <strong>Safety Tip:</strong> Always meet in public places and bring a friend if possible. Never share personal information outside the platform.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Upcycling Section -->
                <section id="upcycling" class="scroll-mt-24">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-[#B59F84] rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Upcycling Section</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- How to request upcycling -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Request Upcycling
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. Navigate to the <strong>"Upcycle"</strong> section in the main menu.</p>
                                    <p>2. Browse available upcyclers in your area or search for specific services.</p>
                                    <p>3. Click on an upcycler's profile to view their portfolio and services.</p>
                                    <p>4. Click <strong>"Book Appointment"</strong> or <strong>"Request Upcycling"</strong>.</p>
                                    <p>5. Fill in the appointment details:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li>Select the item(s) you want to upcycle</li>
                                        <li>Describe what you want done</li>
                                        <li>Choose a preferred date and time</li>
                                        <li>Add any special instructions or requirements</li>
                                    </ul>
                                    <p>6. Submit your request and wait for the upcycler to confirm.</p>
                                    <p>7. You'll receive a notification when the upcycler responds.</p>
                                </div>
                            </div>

                            <!-- How to submit items for upcycle -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How to Submit Items for Upcycle
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>1. When requesting an upcycling appointment, you'll need to specify the items.</p>
                                    <p>2. Take clear photos of the items you want to upcycle.</p>
                                    <p>3. Describe the current condition and what transformation you're looking for.</p>
                                    <p>4. Bring the items to the appointment or arrange for the upcycler to pick them up (if they offer that service).</p>
                                    <p>5. Discuss the project details with the upcycler during the appointment.</p>
                                    <p>6. Agree on timeline, pricing (if applicable), and expectations.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <strong>Tip:</strong> Be clear about your vision and budget to ensure the best results.
                                    </p>
                                </div>
                            </div>

                            <!-- What items can be upcycled -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    What Items Can Be Upcycled
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>Many items can be transformed through upcycling! Common categories include:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li><strong>Furniture:</strong> Chairs, tables, cabinets, dressers</li>
                                        <li><strong>Clothing:</strong> Old garments can be redesigned or repurposed</li>
                                        <li><strong>Accessories:</strong> Bags, jewelry, belts</li>
                                        <li><strong>Home Decor:</strong> Lamps, picture frames, vases</li>
                                        <li><strong>Electronics:</strong> Old devices can be repurposed creatively</li>
                                        <li><strong>Textiles:</strong> Curtains, blankets, fabric scraps</li>
                                        <li><strong>Containers:</strong> Jars, boxes, bottles</li>
                                    </ul>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <strong>Note:</strong> Each upcycler may have their own specialties. Check their profile to see what types of items they work with.
                                    </p>
                                </div>
                            </div>

                            <!-- How pricing works -->
                            <div class="border-l-4 border-[#B59F84] pl-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    How Pricing Works
                                </h3>
                                <div class="text-gray-700 dark:text-gray-300 space-y-2">
                                    <p>Pricing for upcycling services varies depending on the upcycler and project:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li><strong>Free Services:</strong> Some upcyclers may offer free upcycling as part of community initiatives or for simple projects.</li>
                                        <li><strong>Paid Services:</strong> Most upcyclers charge based on:</li>
                                        <ul class="list-disc list-inside ml-6 space-y-1">
                                            <li>Complexity of the project</li>
                                            <li>Materials needed</li>
                                            <li>Time required</li>
                                            <li>Size of the item</li>
                                        </ul>
                                        <li><strong>Discussion Required:</strong> Pricing is typically discussed during the appointment booking or consultation.</li>
                                        <li><strong>Payment Methods:</strong> Payment is usually arranged directly with the upcycler (cash, digital payment, etc.).</li>
                                    </ul>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <strong>Tip:</strong> Always discuss pricing and payment terms before starting the project to avoid misunderstandings.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            
           
        </div>
    </div>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</x-app-layout>

