<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Documentation | Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">

    @include('partials.super-admin-navbar')

    <div class="p-4 sm:ml-64 mt-24">
        <div class="max-w-5xl mx-auto">

            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-red-700"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Super Admin Documentation</h1>
                </div>
                <p class="text-gray-600">Complete technical & operational guide for system administration.</p>
            </div>

            {{-- Role Overview --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-3">
                    <i class="fas fa-shield-alt text-red-600 mr-2"></i>Your Role: Super Administrator
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    As the <strong>Super Administrator</strong>, you have the highest level of access in the IUEA Past Paper Management System. 
                    You oversee all system operations including managing administrators across all faculties, monitoring security, 
                    controlling lecturer access, maintaining system health through audit logs and session management, and configuring 
                    external service connections (Firebase, AI APIs).
                </p>
            </div>

            {{-- Quick Navigation --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <a href="#user-management" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-users text-blue-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">User Management</p>
                </a>
                <a href="#security" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-lock text-green-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Security</p>
                </a>
                <a href="#database" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-database text-cyan-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Database Schemas</p>
                </a>
                <a href="#env-config" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-key text-yellow-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">API Keys & .env</p>
                </a>
                <a href="#lecturer-control" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-chalkboard-teacher text-purple-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Lecturer Control</p>
                </a>
                <a href="#exam-management" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-archive text-orange-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Exam Management</p>
                </a>
                <a href="#server-troubleshooting" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-tools text-red-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Troubleshooting</p>
                </a>
                <a href="#roles" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-sitemap text-gray-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">System Roles</p>
                </a>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: SYSTEM ARCHITECTURE                                      --}}
            {{-- ================================================================== --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-project-diagram text-indigo-600 mr-2"></i>System Architecture Overview
                </h2>
                <p class="text-gray-600 mb-4">The system is built on the following technology stack:</p>

                <div class="grid md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <h3 class="font-semibold text-indigo-800 mb-2"><i class="fas fa-server mr-1"></i> Backend</h3>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li><strong>Laravel 11</strong> (PHP 8.2+)</li>
                            <li>XAMPP / Apache server</li>
                            <li>SQLite for local sessions/cache</li>
                            <li>DomPDF for PDF generation</li>
                        </ul>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4">
                        <h3 class="font-semibold text-orange-800 mb-2"><i class="fas fa-fire mr-1"></i> Firebase (Google Cloud)</h3>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li><strong>Firestore</strong> &mdash; primary database (REST API)</li>
                            <li><strong>Firebase Auth</strong> &mdash; user authentication</li>
                            <li><strong>Firebase Storage</strong> &mdash; file & image storage</li>
                        </ul>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <h3 class="font-semibold text-purple-800 mb-2"><i class="fas fa-robot mr-1"></i> AI Services</h3>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li><strong>Anthropic Claude</strong> &mdash; primary AI (question formatting)</li>
                            <li><strong>OpenAI GPT-4o</strong> &mdash; backup AI provider</li>
                            <li>AI21 &mdash; additional provider</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-medium text-gray-800 mb-2">Data Flow:</h3>
                    <p class="text-gray-600 text-sm">
                        <strong>Lecturer</strong> uploads questions &rarr; stored in Firestore <code class="bg-gray-200 px-1 rounded">Exams</code> collection &rarr; 
                        <strong>Dean</strong> reviews &amp; approves &rarr; <strong>GenAdmin</strong> generates random exam &rarr; 
                        PDF generated via DomPDF &rarr; images fetched from Firebase Storage &rarr; final exam PDF downloaded.
                    </p>
                </div>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: ENV CONFIG & API KEYS                                    --}}
            {{-- ================================================================== --}}
            <div id="env-config" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-key text-yellow-600 mr-2"></i>Environment Configuration &amp; API Keys
                </h2>
                <p class="text-gray-600 mb-4">
                    The <code class="bg-gray-100 px-1 rounded">.env</code> file in the project root contains all sensitive configuration. 
                    You <strong>must</strong> set the following keys for the system to function correctly.
                </p>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-red-800 text-sm font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> Security Warning</p>
                    <p class="text-red-700 text-sm">Never commit the <code class="bg-red-100 px-1 rounded">.env</code> file to version control. It contains private API keys and credentials. Always use <code class="bg-red-100 px-1 rounded">.env.example</code> as a template for new deployments.</p>
                </div>

                {{-- Firebase Keys --}}
                <div class="border-l-4 border-orange-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2"><i class="fas fa-fire text-orange-500 mr-1"></i> Firebase Configuration</h3>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto mb-2">
                        <pre class="text-green-400 text-xs font-mono whitespace-pre">FIREBASE_PROJECT_ID=your-project-id
FIREBASE_CREDENTIALS=/full/path/to/firebase_credentials.json
FIREBASE_DATABASE_URL=https://your-project-default-rtdb.firebaseio.com
FIREBASE_CREDENTIALS_BASE64="base64-encoded-service-account-json"
FIREBASE_STORAGE_BASE_URL=https://firebasestorage.googleapis.com/v0/b/your-bucket.appspot.com/o
FIREBASE_STORAGE_BUCKET=your-bucket.appspot.com</pre>
                    </div>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li><strong>FIREBASE_CREDENTIALS</strong> &mdash; absolute path to the Service Account JSON file. Download from Firebase Console &rarr; Project Settings &rarr; Service Accounts &rarr; Generate New Private Key.</li>
                        <li><strong>FIREBASE_CREDENTIALS_BASE64</strong> &mdash; same JSON file encoded as base64. Used for deployment on servers where file paths are restricted.</li>
                        <li><strong>FIREBASE_STORAGE_BUCKET</strong> &mdash; your Firebase Storage bucket name (found in Firebase Console &rarr; Storage).</li>
                    </ul>
                </div>

                {{-- AI API Keys --}}
                <div class="border-l-4 border-purple-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2"><i class="fas fa-robot text-purple-500 mr-1"></i> AI Assistant API Keys</h3>
                    <p class="text-gray-600 text-sm mb-2">
                        The system uses AI to help lecturers format exam questions. You must add valid API keys for these to work.
                    </p>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto mb-2">
                        <pre class="text-green-400 text-xs font-mono whitespace-pre"># Primary AI Provider (Anthropic Claude) - REQUIRED
ANTHROPIC_API_KEY=sk-ant-api03-your-key-here
AI_MODEL=claude-sonnet-4-20250514

# Backup AI Provider (OpenAI) - OPTIONAL but recommended
OPENAI_API_KEY=sk-proj-your-key-here

# Additional AI Provider
AI21_API_KEY=your-ai21-key-here</pre>
                    </div>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li><strong>ANTHROPIC_API_KEY</strong> &mdash; get from <a href="https://console.anthropic.com/" class="text-blue-600 underline" target="_blank">console.anthropic.com</a> &rarr; API Keys. This is the primary AI used for question formatting, improvement, and simplification.</li>
                        <li><strong>OPENAI_API_KEY</strong> &mdash; get from <a href="https://platform.openai.com/api-keys" class="text-blue-600 underline" target="_blank">platform.openai.com</a> &rarr; API Keys. Used as a backup if Anthropic is unavailable.</li>
                        <li><strong>AI_MODEL</strong> &mdash; the Anthropic model to use. Default: <code class="bg-gray-100 px-1 rounded">claude-sonnet-4-20250514</code>. Can be changed to use newer models.</li>
                    </ul>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                        <p class="text-yellow-800 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i> <strong>Rate Limit:</strong> The AI assistant is rate-limited to 15 requests per minute per user, with a max content length of 50,000 characters per request.</p>
                    </div>
                </div>

                {{-- Other .env Keys --}}
                <div class="border-l-4 border-blue-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2"><i class="fas fa-cog text-blue-500 mr-1"></i> Other Important Settings</h3>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto mb-2">
                        <pre class="text-green-400 text-xs font-mono whitespace-pre"># Application
APP_ENV=production          # Set to 'production' on live server
APP_DEBUG=false             # MUST be false in production
APP_URL=https://yourdomain.com

# Force REST API (no gRPC) - KEEP THESE
GOOGLE_CLOUD_DISABLE_GRPC=true
GOOGLE_APPLICATION_CREDENTIALS_TRANSPORT=rest

# Session
SESSION_DRIVER=database     # Uses SQLite
SESSION_LIFETIME=120        # Minutes before session expires

# Encryption Security
BCRYPT_ROUNDS=12            # Password hashing strength</pre>
                    </div>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li><strong>APP_DEBUG</strong> &mdash; must be <code class="bg-gray-100 px-1 rounded">false</code> in production. When <code class="bg-gray-100 px-1 rounded">true</code>, error details (including credentials) are exposed.</li>
                        <li><strong>GOOGLE_CLOUD_DISABLE_GRPC</strong> &mdash; must remain <code class="bg-gray-100 px-1 rounded">true</code>. The system uses REST API for Firestore instead of gRPC.</li>
                    </ul>
                </div>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: DATABASE SCHEMAS                                         --}}
            {{-- ================================================================== --}}
            <div id="database" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-database text-cyan-600 mr-2"></i>Firestore Database Schema
                </h2>
                <p class="text-gray-600 mb-4">
                    The application uses <strong>Google Cloud Firestore</strong> as the primary database. Data is stored in collections of documents. 
                    Below is the complete schema for each collection.
                </p>

                {{-- Users Collection --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">COLLECTION</span>
                        <h3 class="font-bold text-gray-900 text-lg font-mono">Users</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Stores all user accounts. Document ID = Firebase Auth UID.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border">
                            <thead class="bg-gray-50 text-gray-700 uppercase">
                                <tr>
                                    <th class="px-3 py-2 border">Field</th>
                                    <th class="px-3 py-2 border">Type</th>
                                    <th class="px-3 py-2 border">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr><td class="px-3 py-2 border font-mono">firstName</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">User's first name</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">lastName</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">User's last name</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">email</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Login email (unique)</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">role</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border"><code>superadmin</code>, <code>admin</code>, <code>dean</code>, <code>genadmin</code>, or <code>lecturer</code></td></tr>
                                <tr><td class="px-3 py-2 border font-mono">faculty</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Single faculty assignment (admin/dean/genadmin)</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">faculties</td><td class="px-3 py-2 border">array</td><td class="px-3 py-2 border">Multi-faculty assignment (lecturers). E.g. <code>["FST","FBM"]</code></td></tr>
                                <tr><td class="px-3 py-2 border font-mono">courses</td><td class="px-3 py-2 border">array</td><td class="px-3 py-2 border">Course names assigned to lecturers</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">disabled</td><td class="px-3 py-2 border">boolean</td><td class="px-3 py-2 border">Account enable/disable toggle</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">created_at</td><td class="px-3 py-2 border">string (ISO 8601)</td><td class="px-3 py-2 border">Account creation timestamp</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Exams Collection --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full">COLLECTION</span>
                        <h3 class="font-bold text-gray-900 text-lg font-mono">Exams</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Active exam papers created by lecturers. One document per course unit.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border">
                            <thead class="bg-gray-50 text-gray-700 uppercase">
                                <tr>
                                    <th class="px-3 py-2 border">Field</th>
                                    <th class="px-3 py-2 border">Type</th>
                                    <th class="px-3 py-2 border">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr><td class="px-3 py-2 border font-mono">courseUnit</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Course name (e.g. "Database Systems")</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">courseCode</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Unique course code (e.g. "BIT2201")</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">faculty</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border"><code>FST</code>, <code>FBM</code>, <code>FOE</code>, <code>FOL</code>, or <code>HEC</code></td></tr>
                                <tr><td class="px-3 py-2 border font-mono">format</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Exam format type</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">sections</td><td class="px-3 py-2 border">map</td><td class="px-3 py-2 border"><code>{"A": [html,...], "B": [html,...], "C": [...]}</code> &mdash; questions as HTML strings</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">sectionA_instructions</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Instructions printed above Section A</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">sectionB_instructions</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Instructions printed above Section B</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">sectionC_instructions</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Optional (FOL faculty only)</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">status</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border"><code>Pending Review</code>, <code>Approved</code>, or <code>Declined</code></td></tr>
                                <tr><td class="px-3 py-2 border font-mono">lecturerEmail</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Email of the uploading lecturer</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">lecturerName</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Name of the uploading lecturer</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">marking_guide</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Base64-encoded marking guide file</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">attached_file_type</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">File extension: <code>pdf</code>, <code>doc</code>, <code>docx</code>, <code>xls</code>, <code>xlsx</code></td></tr>
                                <tr><td class="px-3 py-2 border font-mono">comment</td><td class="px-3 py-2 border">string|null</td><td class="px-3 py-2 border">Dean's decline/feedback comment</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">dean_edits</td><td class="px-3 py-2 border">array</td><td class="px-3 py-2 border">History of edits made by the Dean during review</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">dean_comments</td><td class="px-3 py-2 border">array</td><td class="px-3 py-2 border">Per-question comments from the Dean</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">download_count</td><td class="px-3 py-2 border">integer</td><td class="px-3 py-2 border">Number of times this exam has been downloaded</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">created_at</td><td class="px-3 py-2 border">string (ISO 8601)</td><td class="px-3 py-2 border">Upload timestamp</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Courses Collection --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full">COLLECTION</span>
                        <h3 class="font-bold text-gray-900 text-lg font-mono">Courses</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Master list of all course units offered across faculties.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border">
                            <thead class="bg-gray-50 text-gray-700 uppercase">
                                <tr>
                                    <th class="px-3 py-2 border">Field</th>
                                    <th class="px-3 py-2 border">Type</th>
                                    <th class="px-3 py-2 border">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr><td class="px-3 py-2 border font-mono">name</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Course unit name</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">code</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Unique course code (e.g. "BIT2201")</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">program</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Degree program (e.g. "BIT", "MIT", "BBA")</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">year_sem</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Year and semester (e.g. "Year 2/Semester 1")</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">faculty</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border"><code>FST</code>, <code>FBM</code>, <code>FOE</code>, <code>FOL</code>, or <code>HEC</code></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- pastExams Collection --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full">COLLECTION</span>
                        <h3 class="font-bold text-gray-900 text-lg font-mono">pastExams</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Historical past exam PDFs uploaded by admins for student access on the public site.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border">
                            <thead class="bg-gray-50 text-gray-700 uppercase">
                                <tr>
                                    <th class="px-3 py-2 border">Field</th>
                                    <th class="px-3 py-2 border">Type</th>
                                    <th class="px-3 py-2 border">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr><td class="px-3 py-2 border font-mono">courseUnit</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Course name</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">year</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Academic year (e.g. "2024")</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">program</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Degree program</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">examPeriod</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border"><code>April</code>, <code>August</code>, or <code>December</code></td></tr>
                                <tr><td class="px-3 py-2 border font-mono">file_path</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Firebase Storage path (new uploads)</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">file_url</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Signed download URL</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">file</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Base64-encoded PDF (legacy uploads only)</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">faculty</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Faculty code</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">uploaded_by</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Admin email who uploaded</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">download_count</td><td class="px-3 py-2 border">integer</td><td class="px-3 py-2 border">Total downloads tracked</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- AuditLogs & ActiveSessions --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full">COLLECTION</span>
                        <h3 class="font-bold text-gray-900 text-lg font-mono">AuditLogs</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Comprehensive audit trail for all significant system actions.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border">
                            <thead class="bg-gray-50 text-gray-700 uppercase">
                                <tr>
                                    <th class="px-3 py-2 border">Field</th>
                                    <th class="px-3 py-2 border">Type</th>
                                    <th class="px-3 py-2 border">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr><td class="px-3 py-2 border font-mono">timestamp</td><td class="px-3 py-2 border">string (ISO 8601)</td><td class="px-3 py-2 border">When the action occurred</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">user_email</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Who performed the action</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">user_role</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Role of the user at time of action</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">action</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">E.g. <code>login_success</code>, <code>exam_created</code>, <code>exam_approved</code>, <code>user_deleted</code>, <code>bulk_archive</code></td></tr>
                                <tr><td class="px-3 py-2 border font-mono">resource_type</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border"><code>authentication</code>, <code>exam</code>, <code>user</code>, <code>system</code>, <code>security</code>, etc.</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">ip_address</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Client IP address</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">details</td><td class="px-3 py-2 border">map</td><td class="px-3 py-2 border">Extra context (varies per action type)</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-teal-100 text-teal-800 text-xs font-bold px-3 py-1 rounded-full">COLLECTION</span>
                        <h3 class="font-bold text-gray-900 text-lg font-mono">ActiveSessions</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Real-time session tracking for logged-in users. Used by the Active Sessions monitoring page.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border">
                            <thead class="bg-gray-50 text-gray-700 uppercase">
                                <tr>
                                    <th class="px-3 py-2 border">Field</th>
                                    <th class="px-3 py-2 border">Type</th>
                                    <th class="px-3 py-2 border">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr><td class="px-3 py-2 border font-mono">user_email</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Logged-in user's email</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">user_role</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">User's role</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">ip_address</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Login IP</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">device_type</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border"><code>desktop</code>, <code>mobile</code>, or <code>tablet</code></td></tr>
                                <tr><td class="px-3 py-2 border font-mono">browser</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Browser name</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">started_at</td><td class="px-3 py-2 border">string (ISO 8601)</td><td class="px-3 py-2 border">Session start time</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">last_activity</td><td class="px-3 py-2 border">string (ISO 8601)</td><td class="px-3 py-2 border">Last request timestamp</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">is_active</td><td class="px-3 py-2 border">boolean</td><td class="px-3 py-2 border">Set to <code>false</code> on logout/termination</td></tr>
                                <tr><td class="px-3 py-2 border font-mono">terminated_by</td><td class="px-3 py-2 border">string</td><td class="px-3 py-2 border">Email of admin who force-terminated (if applicable)</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: DASHBOARD                                                --}}
            {{-- ================================================================== --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-pie text-indigo-600 mr-2"></i>Dashboard
                </h2>
                <p class="text-gray-600 mb-4">
                    Your dashboard provides a system-wide overview of the platform. It displays key metrics and status indicators for all faculties.
                </p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-medium text-gray-800 mb-2">How to access:</h3>
                    <p class="text-gray-600 text-sm">Navigate to <strong>Dashboard</strong> from the sidebar. This is your landing page after login.</p>
                </div>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: USER MANAGEMENT                                          --}}
            {{-- ================================================================== --}}
            <div id="user-management" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-users-cog text-blue-600 mr-2"></i>User Management
                </h2>
                <p class="text-gray-600 mb-4">
                    You manage all administrative users in the system. This includes Faculty Admins, General Admins (GenAdmins), and Deans.
                </p>

                {{-- Add Admin --}}
                <div class="border-l-4 border-blue-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Adding a New Admin</h3>
                    <ol class="list-decimal list-inside text-gray-600 text-sm space-y-2">
                        <li>Click <strong>"Add Admin"</strong> in the sidebar.</li>
                        <li>Fill in the administrator's details: first name, last name, and email address.</li>
                        <li>Set a secure password (minimum 6 characters).</li>
                        <li>Select the <strong>role</strong>:
                            <ul class="list-disc list-inside ml-4 mt-1 space-y-1">
                                <li><strong>Admin</strong> &mdash; Faculty-level administrator who manages lecturers, courses, and past exams within their faculty.</li>
                                <li><strong>GenAdmin</strong> &mdash; General administrator who generates final randomized exams from the question bank.</li>
                                <li><strong>Dean</strong> &mdash; Faculty dean who reviews, moderates, and approves/declines exam papers.</li>
                            </ul>
                        </li>
                        <li>Assign the appropriate <strong>faculty</strong> (FST, FBM, FOE, FOL, or HEC).</li>
                        <li>Click <strong>"Register"</strong> to create the account.</li>
                    </ol>
                </div>

                {{-- Admin List --}}
                <div class="border-l-4 border-green-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Managing Existing Admins</h3>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-2">
                        <li>Click <strong>"Admin List"</strong> in the sidebar to view all administrators.</li>
                        <li>The list shows all users with roles: admin, genadmin, or dean.</li>
                        <li><strong>Edit</strong>: Click the edit button to modify an admin's name, email, role, or faculty assignment.</li>
                        <li><strong>Delete</strong>: Click the delete button to permanently remove an admin from both Firestore and Firebase Authentication.</li>
                    </ul>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                        <p class="text-yellow-800 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i> <strong>Warning:</strong> Deleting an admin is permanent and cannot be undone. The user is removed from both the <code class="bg-yellow-100 px-1 rounded">Users</code> Firestore collection and Firebase Authentication.</p>
                    </div>
                </div>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: LECTURER CONTROL                                         --}}
            {{-- ================================================================== --}}
            <div id="lecturer-control" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chalkboard-teacher text-purple-600 mr-2"></i>Lecturer Control
                </h2>
                <p class="text-gray-600 mb-4">
                    The Lecturer Control panel gives you system-wide oversight of all lecturers across every faculty.
                </p>

                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-purple-50 rounded-lg p-4">
                        <h3 class="font-semibold text-purple-800 mb-2"><i class="fas fa-toggle-on mr-1"></i> Enable/Disable Lecturers</h3>
                        <p class="text-gray-600 text-sm">Toggle individual lecturers on or off. When disabled, a lecturer cannot log in or upload exam questions. Use <strong>"Toggle All"</strong> to enable or disable all lecturers at once (useful at the start/end of exam periods).</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4">
                        <h3 class="font-semibold text-orange-800 mb-2"><i class="fas fa-eraser mr-1"></i> Clear Course Assignments</h3>
                        <p class="text-gray-600 text-sm">Remove all course assignments from a specific lecturer or use <strong>"Clear All Courses"</strong> to reset every lecturer's course list. This is typically done at the beginning of a new semester.</p>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-medium text-blue-800 mb-1">Typical Semester Workflow:</h3>
                    <ol class="list-decimal list-inside text-blue-700 text-sm space-y-1">
                        <li>At semester start: <strong>Clear all lecturers' courses</strong> to reset assignments.</li>
                        <li>Faculty admins then re-assign courses to lecturers for the new semester.</li>
                        <li>Enable all lecturers to allow question uploads.</li>
                        <li>After the exam period: <strong>Disable all lecturers</strong> to prevent further uploads.</li>
                    </ol>
                </div>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: SECURITY & MONITORING                                    --}}
            {{-- ================================================================== --}}
            <div id="security" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-shield-alt text-green-600 mr-2"></i>Security & Monitoring
                </h2>

                {{-- Audit Logs --}}
                <div class="border-l-4 border-green-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Audit Logs</h3>
                    <p class="text-gray-600 text-sm mb-2">
                        Every significant action in the system is recorded in the <code class="bg-gray-100 px-1 rounded">AuditLogs</code> Firestore collection. The audit log tracks:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                        <li><strong>Login activity</strong> &mdash; successful logins and failed attempts (with IP address).</li>
                        <li><strong>User management</strong> &mdash; when admins or lecturers are created, edited, or deleted.</li>
                        <li><strong>Exam actions</strong> &mdash; uploads, approvals, declines, dean edits, and question modifications.</li>
                        <li><strong>Past exam operations</strong> &mdash; uploads and deletions of past papers.</li>
                        <li><strong>System actions</strong> &mdash; session cleanups, archiving, bulk operations, and access denials.</li>
                    </ul>
                    <p class="text-gray-500 text-sm mt-2">Each log entry includes timestamp, user name, email, role, action type, resource affected, IP address, user agent, session ID, and detailed context.</p>
                </div>

                {{-- Active Sessions --}}
                <div class="border-l-4 border-blue-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Active Sessions</h3>
                    <p class="text-gray-600 text-sm mb-2">
                        Monitor who is currently logged in. Data is stored in the <code class="bg-gray-100 px-1 rounded">ActiveSessions</code> Firestore collection.
                    </p>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                        <li>View all active user sessions with login time, IP address, device type, browser, and OS.</li>
                        <li><strong>Terminate Session</strong> &mdash; force a user out instantly if suspicious activity is detected.</li>
                        <li><strong>Cleanup Sessions</strong> &mdash; remove expired/stale sessions from the system.</li>
                    </ul>
                </div>

                {{-- Download Logs --}}
                <div class="border-l-4 border-purple-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Download Logs</h3>
                    <p class="text-gray-600 text-sm">
                        Track all past exam downloads across the platform. Each log records the file downloaded, the user or visitor, timestamp, and related program information. Use this to monitor exam paper distribution and detect unusual download patterns.
                    </p>
                </div>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: EXAM MANAGEMENT                                          --}}
            {{-- ================================================================== --}}
            <div id="exam-management" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-archive text-orange-600 mr-2"></i>Exam Archiving & Deletion
                </h2>
                <p class="text-gray-600 mb-4">
                    At the end of each academic period, you can archive or delete exam data from the system.
                </p>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-green-50 rounded-lg p-4">
                        <h3 class="font-semibold text-green-800 mb-2"><i class="fas fa-archive mr-1"></i> Archive Exams</h3>
                        <p class="text-gray-600 text-sm">Moves exam data to an archive collection for historical records. Archived exams are no longer active but can be referenced later. This is a safe operation that preserves data.</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4">
                        <h3 class="font-semibold text-red-800 mb-2"><i class="fas fa-trash-alt mr-1"></i> Delete Exams</h3>
                        <p class="text-gray-600 text-sm">Permanently removes exam data from the Firestore <code class="bg-red-100 px-1 rounded">Exams</code> collection. This cannot be undone. Use this only when you are certain the exam data is no longer needed.</p>
                    </div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-4">
                    <p class="text-yellow-800 text-sm"><i class="fas fa-info-circle mr-1"></i> <strong>Tip:</strong> Always archive exams before deleting them. Both operations run asynchronously in the background and you can track their progress via the progress bar.</p>
                </div>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: SERVER & API TROUBLESHOOTING                             --}}
            {{-- ================================================================== --}}
            <div id="server-troubleshooting" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-tools text-red-600 mr-2"></i>Server & API Troubleshooting
                </h2>

                {{-- Firebase Connection --}}
                <div class="border-l-4 border-orange-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2"><i class="fas fa-fire text-orange-500 mr-1"></i> Firebase / Firestore Issues</h3>
                    <div class="space-y-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">Error: "Could not load Firestore credentials"</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>Check that <code class="bg-gray-200 px-1 rounded">FIREBASE_CREDENTIALS</code> in <code class="bg-gray-200 px-1 rounded">.env</code> points to a valid JSON file path.</li>
                                <li>Ensure the <code class="bg-gray-200 px-1 rounded">firebase_credentials.json</code> file exists at the project root and is readable.</li>
                                <li>On production, verify <code class="bg-gray-200 px-1 rounded">FIREBASE_CREDENTIALS_BASE64</code> is correctly encoded.</li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">Error: "gRPC extension not found" or gRPC-related crashes</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>The system uses REST API, not gRPC. Ensure these are in your <code class="bg-gray-200 px-1 rounded">.env</code>:</li>
                                <li><code class="bg-gray-200 px-1 rounded">GOOGLE_CLOUD_DISABLE_GRPC=true</code></li>
                                <li><code class="bg-gray-200 px-1 rounded">GOOGLE_APPLICATION_CREDENTIALS_TRANSPORT=rest</code></li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">Error: "Permission denied" on Firestore operations</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>The Firebase service account must have the <strong>Cloud Datastore User</strong> role or higher.</li>
                                <li>Check in Google Cloud Console &rarr; IAM &rarr; verify the service account email has Firestore access.</li>
                                <li>Also check Firestore Security Rules in Firebase Console if they're restricting access.</li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">Error: Firebase Storage upload/download failures</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>Verify <code class="bg-gray-200 px-1 rounded">FIREBASE_STORAGE_BUCKET</code> matches your Firebase project's bucket name.</li>
                                <li>Check Firebase Storage rules in the Firebase Console allow read/write for authenticated users.</li>
                                <li>Ensure the service account has <strong>Storage Object Admin</strong> or <strong>Storage Object Creator</strong> role.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- AI API Issues --}}
                <div class="border-l-4 border-purple-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2"><i class="fas fa-robot text-purple-500 mr-1"></i> AI Assistant Issues</h3>
                    <div class="space-y-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">"AI Assistant is not responding" or "API key invalid"</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>Verify <code class="bg-gray-200 px-1 rounded">ANTHROPIC_API_KEY</code> is set and valid in <code class="bg-gray-200 px-1 rounded">.env</code>.</li>
                                <li>Check the key hasn't expired or hit its spending limit at <a href="https://console.anthropic.com/" class="text-blue-600 underline" target="_blank">console.anthropic.com</a>.</li>
                                <li>The health endpoint can be tested at: <code class="bg-gray-200 px-1 rounded">/ai-assistant/health</code></li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">"Rate limit exceeded"</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>The AI assistant is limited to 15 requests per minute. Ask users to wait briefly before retrying.</li>
                                <li>If Anthropic API hits its rate limit, check if you need to upgrade your Anthropic plan tier.</li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">Switching to the backup AI (OpenAI)</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>Set <code class="bg-gray-200 px-1 rounded">AI_PROVIDER=openai</code> in the <code class="bg-gray-200 px-1 rounded">.env</code> file.</li>
                                <li>Ensure <code class="bg-gray-200 px-1 rounded">OPENAI_API_KEY</code> is set with a valid key.</li>
                                <li>Run <code class="bg-gray-200 px-1 rounded">php artisan config:clear</code> to reload config after changes.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Laravel / Server Issues --}}
                <div class="border-l-4 border-blue-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2"><i class="fas fa-server text-blue-500 mr-1"></i> Laravel / Server Issues</h3>
                    <div class="space-y-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">500 Internal Server Error</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>Check <code class="bg-gray-200 px-1 rounded">storage/logs/laravel.log</code> for the detailed error message.</li>
                                <li>Ensure all file permissions are correct: <code class="bg-gray-200 px-1 rounded">storage/</code> and <code class="bg-gray-200 px-1 rounded">bootstrap/cache/</code> must be writable.</li>
                                <li>Run <code class="bg-gray-200 px-1 rounded">php artisan config:clear && php artisan cache:clear && php artisan view:clear</code></li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">"Port 8000 already in use" when starting the server</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>Kill the existing process: <code class="bg-gray-200 px-1 rounded">kill -9 $(lsof -t -i:8000)</code></li>
                                <li>Or start on a different port: <code class="bg-gray-200 px-1 rounded">php artisan serve --port=8001</code></li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">Session / Authentication problems</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>Sessions are stored in SQLite (<code class="bg-gray-200 px-1 rounded">SESSION_DRIVER=database</code>).</li>
                                <li>If users can't log in, check that the <code class="bg-gray-200 px-1 rounded">database/database.sqlite</code> file exists and the sessions table is created.</li>
                                <li>Run <code class="bg-gray-200 px-1 rounded">php artisan migrate</code> to create the sessions table if missing.</li>
                                <li>Clear sessions: <code class="bg-gray-200 px-1 rounded">php artisan session:table && php artisan migrate</code></li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-semibold text-gray-700">PDF generation failures</p>
                            <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                <li>DomPDF requires the <code class="bg-gray-200 px-1 rounded">public/pdf_images/</code> directory to be writable.</li>
                                <li>Check that Firebase Storage images are accessible (valid signed URLs).</li>
                                <li>If images don't appear in PDFs, check logs for "Failed to download image" errors.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Common Commands --}}
                <div class="border-l-4 border-green-500 pl-4">
                    <h3 class="font-semibold text-gray-800 mb-2"><i class="fas fa-terminal text-green-500 mr-1"></i> Useful Server Commands</h3>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-green-400 text-xs font-mono whitespace-pre"># Start the development server
php artisan serve

# Clear all caches (run after .env changes)
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Check Laravel logs
tail -f storage/logs/laravel.log

# Run database migrations (sessions/cache tables)
php artisan migrate

# Check route list
php artisan route:list

# Check PHP version and extensions
php -v && php -m | grep -i "curl\|json\|mbstring\|openssl"</pre>
                    </div>
                </div>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: SYSTEM ROLES                                             --}}
            {{-- ================================================================== --}}
            <div id="roles" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-sitemap text-gray-600 mr-2"></i>System Roles Reference
                </h2>
                <p class="text-gray-600 mb-4">Understanding the role hierarchy helps you manage the system effectively.</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Scope</th>
                                <th class="px-4 py-3">Key Responsibilities</th>
                                <th class="px-4 py-3">Middleware</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 font-medium text-red-700">Super Admin</td>
                                <td class="px-4 py-3">System-wide</td>
                                <td class="px-4 py-3">Manage all admins, monitor security, control lecturers globally, archive/delete exams, configure API keys</td>
                                <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-1 rounded">EnsureSuperAdminRole</code></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-blue-700">Admin</td>
                                <td class="px-4 py-3">Faculty</td>
                                <td class="px-4 py-3">Manage lecturers, courses, and past exam papers within their assigned faculty</td>
                                <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-1 rounded">EnsureAdminRole</code></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-green-700">Dean</td>
                                <td class="px-4 py-3">Faculty</td>
                                <td class="px-4 py-3">Review, edit, approve or decline exam papers; monitor faculty activity</td>
                                <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-1 rounded">EnsureDeanRole</code></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-purple-700">GenAdmin</td>
                                <td class="px-4 py-3">Faculty</td>
                                <td class="px-4 py-3">Generate randomized final exams from approved question banks</td>
                                <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-1 rounded">EnsureGenAdminRole</code></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-orange-700">Lecturer</td>
                                <td class="px-4 py-3">Courses</td>
                                <td class="px-4 py-3">Upload exam questions for assigned courses, manage section instructions, attach marking guide</td>
                                <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-1 rounded">EnsureLecturerRole</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ================================================================== --}}
            {{--  SECTION: FACULTY CODES                                            --}}
            {{-- ================================================================== --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-university text-gray-600 mr-2"></i>Faculty Codes Reference
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">Code</th>
                                <th class="px-4 py-3">Full Name</th>
                                <th class="px-4 py-3">Exam Section A (random)</th>
                                <th class="px-4 py-3">Exam Section B (random)</th>
                                <th class="px-4 py-3">Exam Section C (random)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold">FST</td>
                                <td class="px-4 py-3">Faculty of Science & Technology</td>
                                <td class="px-4 py-3">1 question</td>
                                <td class="px-4 py-3">6 questions</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold">FBM</td>
                                <td class="px-4 py-3">Faculty of Business & Management</td>
                                <td class="px-4 py-3">1 question</td>
                                <td class="px-4 py-3">6 questions</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold">FOE</td>
                                <td class="px-4 py-3">Faculty of Engineering</td>
                                <td class="px-4 py-3">3 questions</td>
                                <td class="px-4 py-3">3 questions</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold">HEC</td>
                                <td class="px-4 py-3">Higher Education Certificate</td>
                                <td class="px-4 py-3">10 questions</td>
                                <td class="px-4 py-3">6 questions</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold">FOL</td>
                                <td class="px-4 py-3">Faculty of Law</td>
                                <td class="px-4 py-3">1 question</td>
                                <td class="px-4 py-3">2 questions</td>
                                <td class="px-4 py-3">4 questions</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Support --}}
            <div class="bg-gradient-to-r from-red-800 to-red-900 rounded-xl p-6 text-white mb-8">
                <h2 class="text-xl font-semibold mb-2"><i class="fas fa-headset mr-2"></i>Need Help?</h2>
                <p class="text-red-100 text-sm mb-3">
                    If you encounter any issues or need technical support, contact the system development team. 
                    As super admin, you have full access to audit logs which can help diagnose most issues.
                </p>
                <div class="bg-white/10 rounded-lg p-3 mt-2">
                    <p class="text-red-100 text-xs font-mono">
                        Quick diagnostics: Check <span class="text-yellow-300">storage/logs/laravel.log</span> | 
                        Test AI at <span class="text-yellow-300">/ai-assistant/health</span> | 
                        Verify Firebase at <span class="text-yellow-300">Firebase Console &rarr; Project Settings</span>
                    </p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
