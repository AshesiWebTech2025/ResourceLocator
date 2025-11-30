<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$page_title = "Ashesi Resource Locator - Authentication";

//retrieveing any session messages
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';

//clearing any session variables immediately after retrieval
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Configure Tailwind with the Ashesi theme colors and font -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ashesi-maroon': '#800020', // Deep maroon color
                        'ashesi-light': '#fef2f2', // Very light pink for accents
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-50 font-sans antialiased flex items-center justify-center min-h-screen p-4 loginBody">
    <!-- auth card -->
    <div class="w-full max-w-md">
        <!-- app header - UPDATED FOR VISIBILITY -->
        <!-- Added a semi-transparent background (bg-white/80) and backdrop blur to make the text pop against the bright background image. -->
        <div class="text-center mb-8 p-4 rounded-xl shadow-xl bg-white/80 backdrop-blur-sm border border-gray-200">
            <h1 class="text-3xl font-extrabold text-ashesi-maroon mb-2">Ashesi Resource Locator</h1>
            <!-- Changed text-gray-500 to text-gray-800 for better contrast -->
            <p class="text-gray-800 text-lg font-semibold">Student Access Portal</p>
        </div>
        <!-- system display message -->
        <?php if ($message): ?>
            <div class="mb-6">
                <div class="<?php echo $message_type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> 
                            border-l-4 p-4 rounded-lg" role="alert">
                    <p class="font-bold"><?php echo $message_type === 'success' ? 'Success!' : 'Error!'; ?></p>
                    <p><?php echo htmlspecialchars($message); ?></p>
                </div>
            </div>
        <?php endif; ?>
        <!-- form card -->
        <div class="bg-white p-8 md:p-10 rounded-xl shadow-2xl border border-gray-100">
            <!--login form -->
            <div id="login-form">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Welcome Back!</h2>
                
                <form method="POST" action="../backend/loginSignupPreprocessor.php">
                    <!-- hidden action -->
                    <input type="hidden" name="action" value="login">

                    <div class="mb-5">
                        <label for="login-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="login-email" name="login_email" placeholder="student@ashesi.edu.gh" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon/50 focus:border-ashesi-maroon transition duration-150">
                    </div>
                    
                    <div class="mb-6">
                        <label for="login-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="login-password" name="login_password" placeholder="••••••••" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon/50 focus:border-ashesi-maroon transition duration-150">
                    </div>
                    
                    <button type="submit"
                            class="w-full bg-ashesi-maroon text-white font-semibold py-3 rounded-lg shadow-lg hover:bg-ashesi-maroon/90 transition duration-200">
                        Log In
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600">
                    Don't have an account? 
                    <button onclick="toggleForm('signup')" class="text-ashesi-maroon font-medium hover:text-ashesi-maroon/80 transition duration-150 underline">
                        Sign Up
                    </button>
                </p>
            </div>
            <!-- hidden signup form -->
            <div id="signup-form" class="hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Create Account</h2>
                
                <form id="signupForm" method="POST" action="../backend/loginSignupPreprocessor.php">
                    <!-- hidden failed to identify action-->
                    <input type="hidden" name="action" value="signup">

                     <div class="mb-5">
                        <label for="signup-name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="signup-name" name="name" placeholder="Kwame Ofori" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon/50 focus:border-ashesi-maroon transition duration-150">
                    </div>

                    <div class="mb-5">
                        <label for="signup-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="signup-email" name="email" placeholder="student@ashesi.edu.gh" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon/50 focus:border-ashesi-maroon transition duration-150">
                        <p id="email-error" class="text-sm text-red-500 mt-1 hidden">Email must end with @ashesi.edu.gh</p>
                    </div>

                    <div class="mb-5">
                        <label for="signup-role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select id="signup-role" name="role" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon/50 focus:border-ashesi-maroon transition duration-150">
                            <option value="" disabled selected>Select your role</option>
                            <!-- user should not be able to signup as admin -->
                            <!-- <option value="Admin">Admin</option>  -->                              
                            <option value="Student">Student</option>
                            <option value="Faculty">Faculty</option>
                            <option value="Staff">Staff</option>
                            <option value="Visitor">Visitor</option>
                        </select>
                    </div>
                    
                    <div class="mb-5">
                        <label for="signup-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="signup-password" name="password" placeholder="••••••••" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon/50 focus:border-ashesi-maroon transition duration-150">
                        <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div id="strength-bar" class="h-full transition-all duration-300 ease-in-out"></div>
                        </div>
                        <p id="strength-text" class="text-xs mt-1 text-gray-500"></p>
                    </div>

                    <div class="mb-6">
                        <label for="signup-confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="signup-confirm-password" name="confirm_password" placeholder="••••••••" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon/50 focus:border-ashesi-maroon transition duration-150">
                         <p id="confirm-error" class="text-sm text-red-500 mt-1 hidden">Passwords do not match.</p>
                    </div>
                    
                    <button type="submit" id="signup-submit"
                            class="w-full bg-ashesi-maroon text-white font-semibold py-3 rounded-lg shadow-lg hover:bg-ashesi-maroon/90 transition duration-200">
                        Sign Up
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600">
                    Already have an account? 
                    <button onclick="toggleForm('login')" class="text-ashesi-maroon font-medium hover:text-ashesi-maroon/80 transition duration-150 underline">
                        Log In
                    </button>
                </p>
            </div>

        </div>
    </div>
    <!-- JavaScript for toggling forms and client-side validation -->
    <script>
        const style = document.createElement('style');
        style.textContent = `
            .strength-low { background-color: #f87171; width: 33%; }
            .strength-medium { background-color: #facc15; width: 66%; }
            .strength-high { background-color: #4ade80; width: 100%; }
        `;
        document.head.appendChild(style);
        function toggleForm(view) {
            const loginForm = document.getElementById('login-form');
            const signupForm = document.getElementById('signup-form');

            if (view === 'login') {
                loginForm.classList.remove('hidden');
                signupForm.classList.add('hidden');
            } else if (view === 'signup') {
                loginForm.classList.add('hidden');
                signupForm.classList.remove('hidden');
            }
        }
        const signupForm = document.getElementById('signupForm');
        const emailInput = document.getElementById('signup-email');
        const emailError = document.getElementById('email-error');
        const passwordInput = document.getElementById('signup-password');
        const confirmPasswordInput = document.getElementById('signup-confirm-password');
        const confirmError = document.getElementById('confirm-error');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');
        const submitBtn = document.getElementById('signup-submit');
        const ASHESI_DOMAIN = '@ashesi.edu.gh';

        function updateStrengthIndicator(strength) {
            strengthBar.className = 'h-full transition-all duration-300 ease-in-out';
            
            if (strength < 2) {
                strengthBar.classList.add('strength-low');
                strengthText.textContent = 'Weak';
                strengthText.classList.remove('text-green-500', 'text-yellow-500');
                strengthText.classList.add('text-red-500');
            } else if (strength === 2) {
                strengthBar.classList.add('strength-medium');
                strengthText.textContent = 'Medium';
                strengthText.classList.remove('text-red-500', 'text-green-500');
                strengthText.classList.add('text-yellow-500');
            } else if (strength >= 3) {
                strengthBar.classList.add('strength-high');
                strengthText.textContent = 'Strong';
                strengthText.classList.remove('text-red-500', 'text-yellow-500');
                strengthText.classList.add('text-green-500');
            } else {
                 strengthBar.style.width = '0';
                 strengthText.textContent = '';
            }
        }
        
        function checkPasswordStrength(password) {
            let strength = 0;
            const lengthRegex = /.{8,}/; 
            const lowerCaseRegex = /[a-z]/;
            const upperCaseRegex = /[A-Z]/;
            const numberRegex = /[0-9]/;
            const specialCharRegex = /[^A-Za-z0-9]/;

            if (lengthRegex.test(password)) strength++;
            if (lowerCaseRegex.test(password) && upperCaseRegex.test(password)) strength++;
            if (numberRegex.test(password)) strength++;
            if (specialCharRegex.test(password)) strength++;
            return Math.min(strength, 4);
        }

        function validateForm() {
            let isValid = true;
            //Ashesi Email Validation
            if (!emailInput.value.endsWith(ASHESI_DOMAIN)) {
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-red-500');
                isValid = false;
            } else {
                emailError.classList.add('hidden');
                emailInput.classList.remove('border-red-500');
            }
            //password match validation
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmError.classList.remove('hidden');
                confirmPasswordInput.classList.add('border-red-500');
                isValid = false;
            } else {
                confirmError.classList.add('hidden');
                confirmPasswordInput.classList.remove('border-red-500');
            }
            //password strength check. Submissioin is prevented if password is weak
            const strength = checkPasswordStrength(passwordInput.value);
            if (passwordInput.value.length > 0 && strength < 2) {
                isValid = false;//preventing submission if the password is too weak
            }
            //disabling or enabling the button based on validity
            submitBtn.disabled = !isValid; 
            if (!isValid) {
                 submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                 submitBtn.classList.remove('hover:bg-ashesi-maroon/90');
            } else {
                 submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                 submitBtn.classList.add('hover:bg-ashesi-maroon/90');
            }

            return isValid;
        }
        //adding event listeners
        passwordInput.addEventListener('input', (e) => {
            const strength = checkPasswordStrength(e.target.value);
            updateStrengthIndicator(strength);
            validateForm(); 
        });
        emailInput.addEventListener('input', validateForm);
        confirmPasswordInput.addEventListener('input', validateForm);
        //prevening form submission if client-side validation fails
        signupForm.addEventListener('submit', (e) => {
            if (!validateForm()) {
                e.preventDefault();
                //php error messages will take over
            }
        });
        //iinitializing form view.Primarily used when form fails, 
        document.addEventListener('DOMContentLoaded', () => {
             //if any server-side message exists (usually means failure), show the signup form if fields were posted
             const urlParams = new URLSearchParams(window.location.search);
             const defaultView = urlParams.get('view') || 'login';
             toggleForm(defaultView);
        });
    </script>
</body>
</html>