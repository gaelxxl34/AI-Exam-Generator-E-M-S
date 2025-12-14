<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dean Review - {{ $exam['courseUnit'] ?? 'Exam' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    
    <!-- AI Assistant -->
    <script src="{{ asset('assets/js/ai-assistant.js') }}"></script>
    
    <style>
        /* Clean Minimal Design */
        .question-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 8px;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        
        .question-item:hover { border-color: #3b82f6; }
        .question-item.expanded { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        .question-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            cursor: pointer;
            user-select: none;
            transition: background 0.15s ease;
        }
        
        .question-header:hover { background: #f9fafb; }
        
        .question-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            color: #374151;
        }
        
        .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .question-preview {
            font-size: 13px;
            color: #6b7280;
            max-width: 400px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .question-status { display: flex; align-items: center; gap: 8px; }
        
        .status-badge {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 9999px;
            font-weight: 500;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-reviewed { background: #d1fae5; color: #065f46; }
        
        .expand-icon { transition: transform 0.2s ease; color: #9ca3af; }
        .question-item.expanded .expand-icon { transform: rotate(180deg); }
        
        .question-content {
            display: none;
            padding: 16px;
            border-top: 1px solid #e5e7eb;
            background: #fafafa;
            /* Center the editor container */
            display: none;
        }
        
        .question-item.expanded .question-content { 
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        /* Paper-width container for A4 size */
        .paper-width-wrapper {
            width: 100%;
            max-width: 210mm; /* A4 width */
            background: #f0f0f0;
            padding: 20px;
            border-radius: 8px;
        }
        
        .paper-content {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15mm; /* Standard A4 margins */
            width: 100%;
            max-width: 180mm; /* A4 content width (210mm - 30mm margins) */
            margin: 0 auto;
        }
        
        .paper-width-notice {
            text-align: center;
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 12px;
            padding: 6px 12px;
            background: #e0f2fe;
            border-radius: 4px;
            border: 1px dashed #0ea5e9;
        }
        
        .editor-container {
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            overflow: hidden;
        }
        
        /* Force editor to respect paper width */
        .paper-content .note-editor {
            max-width: 100% !important;
        }
        
        .paper-content .note-editable {
            max-width: 100% !important;
            overflow-x: auto !important;
        }
        
        /* Ensure tables don't overflow */
        .paper-content .note-editable table {
            max-width: 100% !important;
            table-layout: fixed !important;
        }
        
        .paper-content .note-editable img {
            max-width: 100% !important;
            height: auto !important;
        }
        
        .note-editor { border: none !important; }
        .note-editor .note-editing-area { background: white; }

        .note-editor .note-editing-area .note-editable {
            font-family: 'Times New Roman', Georgia, serif !important;
            font-size: 12pt !important;
            line-height: 1.5 !important;
            color: #000 !important;
            padding: 16px !important;
            min-height: 150px;
            max-height: 350px;
            overflow-y: auto;
        }

        .note-toolbar {
            background: #f9fafb !important;
            border-bottom: 1px solid #e5e7eb !important;
            padding: 8px !important;
        }

        .note-toolbar .note-btn { padding: 4px 8px !important; font-size: 12px !important; }
        
        .note-btn-group .btn-ai-assistant,
        .note-toolbar .note-btn[data-name="aiAssistant"] {
            background: #2563eb !important;
            color: white !important;
            border: none !important;
            border-radius: 4px !important;
            font-weight: 500 !important;
            padding: 5px 8px !important;
        }

        .note-btn-group .btn-ai-assistant:hover,
        .note-toolbar .note-btn[data-name="aiAssistant"]:hover { background: #1d4ed8 !important; }
        
        .action-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        
        .section-container { margin-bottom: 24px; }
        
        .section-questions {
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 12px;
            background: #f9fafb;
        }
        
        .feedback-toggle {
            padding: 8px 12px;
            background: #f3f4f6;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            color: #4b5563;
            transition: background 0.15s;
        }
        
        .feedback-toggle:hover { background: #e5e7eb; }
        
        .feedback-panel {
            margin-top: 12px;
            padding: 12px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        
        .edit-history {
            background: #fef9c3;
            border-left: 3px solid #eab308;
            padding: 8px 12px;
            margin-bottom: 12px;
            border-radius: 0 6px 6px 0;
            font-size: 12px;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }
        
        .stat-value { font-size: 24px; font-weight: 700; color: #1f2937; }
        .stat-label { font-size: 12px; color: #6b7280; margin-top: 4px; }
        
        .note-editable table {
            border-collapse: collapse !important;
            width: 100% !important;
            border: 1px solid #000 !important;
            margin: 10px 0 !important;
        }

        .note-editable table td,
        .note-editable table th {
            border: 1px solid #000 !important;
            padding: 6px 8px !important;
        }
        
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>

<body class="bg-gray-100">

    @include('partials.dean-navbar')

    <div class="p-4 sm:ml-64 mt-16">
        <div class="max-w-5xl mx-auto">
            
            <!-- Back Link -->
            <a href="{{ route('dean.moderation') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm mb-4">
                <i class="fas fa-arrow-left mr-2"></i> Back to Moderation List
            </a>
            
            <!-- Header Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $exam['courseUnit'] }}</h1>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mt-2">
                            <span><i class="fas fa-hashtag mr-1"></i>{{ $exam['courseCode'] ?? 'N/A' }}</span>
                            <span><i class="fas fa-user mr-1"></i>{{ $exam['lecturerEmail'] ?? 'N/A' }}</span>
                            <span><i class="fas fa-building mr-1"></i>{{ $exam['faculty'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $statusColors = [
                                'Pending Review' => 'bg-yellow-100 text-yellow-800',
                                'Approved' => 'bg-green-100 text-green-800',
                                'Declined' => 'bg-red-100 text-red-800',
                            ];
                            $statusClass = $statusColors[$exam['status'] ?? 'Pending Review'] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusClass }}">
                            {{ $exam['status'] ?? 'Pending Review' }}
                        </span>
                        <a href="{{ route('preview.pdf', ['courseUnit' => $exam['courseUnit']]) }}" 
                           target="_blank"
                           class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">
                            <i class="fas fa-eye mr-2"></i> Preview PDF
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats & Actions Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-card">
                    <div class="stat-value text-blue-600">{{ $totalQuestions ?? 0 }}</div>
                    <div class="stat-label">Total Questions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value text-green-600" id="reviewedCount">0</div>
                    <div class="stat-label">Reviewed</div>
                </div>
                <div class="stat-card col-span-2">
                    <div class="flex gap-2 justify-center">
                        <form method="POST" action="{{ route('course.approve', ['id' => $exam['id']]) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition">
                                <i class="fas fa-check mr-1"></i> Approve
                            </button>
                        </form>
                        <button onclick="openDeclineModal()" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition">
                            <i class="fas fa-times mr-1"></i> Decline
                        </button>
                    </div>
                    <div class="stat-label mt-2">Quick Actions</div>
                </div>
            </div>
            
            <!-- Questions List -->
            @php 
                $sections = $exam['sections'] ?? [];
                ksort($sections);
                $globalQuestionIndex = 0;
            @endphp
            
            @foreach ($sections as $sectionName => $questions)
                <div class="section-container">
                    <div class="section-header">
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-semibold">Section {{ $sectionName }}</span>
                            <span class="text-sm opacity-80">({{ count($questions) }} questions)</span>
                        </div>
                        <button onclick="expandAllInSection('{{ $sectionName }}')" class="text-xs bg-white/20 hover:bg-white/30 px-3 py-1 rounded transition">
                            Expand All
                        </button>
                    </div>
                    
                    <div class="section-questions" id="section-{{ $sectionName }}">
                        @foreach ($questions as $questionIndex => $question)
                            @php 
                                $globalQuestionIndex++;
                                $questionId = "{$sectionName}-{$questionIndex}";
                                $plainText = strip_tags($question);
                                $preview = strlen($plainText) > 60 ? substr($plainText, 0, 60) . '...' : $plainText;
                                
                                $hasEdits = false;
                                $questionEdits = [];
                                if(isset($exam['dean_edits']) && is_array($exam['dean_edits'])) {
                                    $questionEdits = array_filter($exam['dean_edits'], function($edit) use ($sectionName, $questionIndex) {
                                        return ($edit['section'] ?? '') === $sectionName && ($edit['questionIndex'] ?? -1) == $questionIndex;
                                    });
                                    $hasEdits = count($questionEdits) > 0;
                                }
                                
                                $questionComments = [];
                                if(isset($exam['dean_comments']) && is_array($exam['dean_comments'])) {
                                    $questionComments = array_filter($exam['dean_comments'], function($c) use ($sectionName, $questionIndex) {
                                        return ($c['section'] ?? '') === $sectionName && ($c['questionIndex'] ?? -1) == $questionIndex;
                                    });
                                }
                            @endphp
                            
                            <div class="question-item" id="question-{{ $questionId }}" data-section="{{ $sectionName }}" data-index="{{ $questionIndex }}">
                                <div class="question-header" onclick="toggleQuestion('{{ $questionId }}')">
                                    <div class="question-title">
                                        <span class="question-number">{{ $sectionName }}{{ $questionIndex + 1 }}</span>
                                        <span class="question-preview">{{ $preview }}</span>
                                    </div>
                                    <div class="question-status">
                                        @if($hasEdits)
                                            <span class="status-badge bg-yellow-100 text-yellow-700">
                                                <i class="fas fa-edit mr-1"></i>Edited
                                            </span>
                                        @endif
                                        @if(count($questionComments) > 0)
                                            <span class="status-badge bg-blue-100 text-blue-700">
                                                <i class="fas fa-comment mr-1"></i>{{ count($questionComments) }}
                                            </span>
                                        @endif
                                        <span class="status-badge status-pending" id="status-{{ $questionId }}">Pending</span>
                                        <i class="fas fa-chevron-down expand-icon"></i>
                                    </div>
                                </div>
                                
                                <div class="question-content">
                                    <!-- Paper Width Wrapper -->
                                    <div class="paper-width-wrapper">
                                        <div class="paper-width-notice">
                                            <i class="fas fa-ruler-horizontal mr-1"></i>
                                            Editor width matches A4 paper (210mm) to prevent PDF truncation
                                        </div>
                                        
                                        @if($hasEdits)
                                            <div class="edit-history" style="max-width: 180mm; margin: 0 auto 12px auto;">
                                                <i class="fas fa-history mr-1"></i>
                                                <strong>Previous Edits:</strong>
                                                @foreach($questionEdits as $edit)
                                                    {{ $edit['dean_name'] ?? 'Dean' }} 
                                                    ({{ isset($edit['edited_at']) ? \Carbon\Carbon::parse($edit['edited_at'])->diffForHumans() : '' }})
                                                    @if(!empty($edit['reason'])) - "{{ $edit['reason'] }}" @endif
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <div class="paper-content">
                                            <div class="editor-container">
                                                <form class="dean-edit-form"
                                                    action="{{ route('dean.update.question', ['courseUnit' => $exam['courseUnit'], 'sectionName' => $sectionName, 'questionIndex' => $questionIndex]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="exam_id" value="{{ $exam['id'] }}">
                                                    <textarea id="editor-{{ $questionId }}" class="summernote-editor" name="question">{!! $question !!}</textarea>
                                                </form>
                                            </div>
                                            
                                            <!-- Actions below paper -->
                                            <div class="action-row" style="border-top: none; padding-top: 16px; margin-top: 16px;">
                                                <div class="flex items-center gap-2">
                                                    <input type="text" name="edit_reason" form="form-{{ $questionId }}"
                                                           placeholder="Reason for edit (required)"
                                                           class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                           style="width: 220px;">
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" onclick="markAsReviewed('{{ $questionId }}')"
                                                            class="px-3 py-1.5 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition"
                                                            id="review-btn-{{ $questionId }}">
                                                        <i class="fas fa-check mr-1"></i> Mark Reviewed
                                                    </button>
                                                    <button type="button" onclick="saveQuestion('{{ $questionId }}')"
                                                            class="px-4 py-1.5 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                                                        <i class="fas fa-save mr-1"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Feedback section - also within paper width -->
                                        <div class="mt-3" style="max-width: 180mm; margin-left: auto; margin-right: auto;">
                                            <div class="feedback-toggle" onclick="toggleFeedback('{{ $questionId }}')">
                                                <i class="fas fa-comment-dots mr-2"></i>
                                                Add Feedback / Comment
                                                @if(count($questionComments) > 0)
                                                    <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-blue-500 rounded-full">
                                                        {{ count($questionComments) }}
                                                    </span>
                                                @endif
                                                <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                            </div>
                                            
                                            <div class="feedback-panel hidden" id="feedback-{{ $questionId }}">
                                                @if(count($questionComments) > 0)
                                                    <div class="mb-4 space-y-2">
                                                        @foreach($questionComments as $comment)
                                                            <div class="bg-blue-50 border-l-3 border-blue-400 p-2 rounded text-sm">
                                                                <span class="font-medium text-blue-800">{{ $comment['dean_name'] ?? 'Dean' }}</span>
                                                                <span class="text-xs text-blue-600 ml-2">
                                                                    {{ isset($comment['created_at']) ? \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() : '' }}
                                                                </span>
                                                                <p class="text-gray-700 mt-1">{{ $comment['comment'] ?? '' }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                
                                                <div class="flex gap-2">
                                                    <input type="text" id="comment-input-{{ $questionId }}" 
                                                           placeholder="Type your feedback..."
                                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                                    <button onclick="submitFeedback('{{ $sectionName }}', {{ $questionIndex }}, '{{ $questionId }}')"
                                                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            
        </div>
    </div>

    <!-- Decline Modal -->
    <div id="declineModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4 text-red-600">
                <i class="fas fa-times-circle mr-2"></i>Decline Exam
            </h2>
            <form id="declineForm" method="POST" action="{{ route('course.decline', ['id' => $exam['id']]) }}">
                @csrf
                <textarea name="comment" placeholder="Enter reason for declining (required)..." required
                    class="w-full p-3 border rounded-lg mb-4 focus:ring-2 focus:ring-red-500 text-sm" rows="3"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeDeclineModal()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-times mr-1"></i> Decline
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const reviewedQuestions = new Set();
        const totalQuestions = {{ $totalQuestions ?? 0 }};
        let editorsInitialized = {};
        
        function toggleQuestion(questionId) {
            const item = document.getElementById('question-' + questionId);
            const wasExpanded = item.classList.contains('expanded');
            item.classList.toggle('expanded');
            if (!wasExpanded && !editorsInitialized[questionId]) {
                initializeEditor(questionId);
            }
        }
        
        function initializeEditor(questionId) {
            const $editor = $('#editor-' + questionId);
            if ($editor.length && !editorsInitialized[questionId]) {
                $editor.summernote({
                    height: 200,
                    minHeight: 150,
                    maxHeight: 350,
                    focus: false,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontsize', ['fontsize']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['codeview']],
                        ['ai', ['aiAssistant']]
                    ],
                    buttons: {
                        aiAssistant: function(context) {
                            const ui = $.summernote.ui;
                            const button = ui.button({
                                contents: '<i class="fas fa-pen-fancy"></i> AI Help',
                                tooltip: 'AI Writing Assistant',
                                className: 'btn-ai-assistant',
                                click: function() {
                                    const $ed = context.$note;
                                    const editorId = $ed.attr('id');
                                    const selector = editorId ? '#' + editorId : '.summernote-editor';
                                    if (typeof showAIAssistantModal === 'function') {
                                        showAIAssistantModal(selector);
                                    }
                                }
                            });
                            return button.render();
                        }
                    }
                });
                editorsInitialized[questionId] = true;
            }
        }
        
        function expandAllInSection(sectionName) {
            const section = document.getElementById('section-' + sectionName);
            section.querySelectorAll('.question-item').forEach(item => {
                if (!item.classList.contains('expanded')) {
                    const questionId = item.id.replace('question-', '');
                    item.classList.add('expanded');
                    if (!editorsInitialized[questionId]) {
                        initializeEditor(questionId);
                    }
                }
            });
        }
        
        function markAsReviewed(questionId) {
            const btn = document.getElementById('review-btn-' + questionId);
            const statusBadge = document.getElementById('status-' + questionId);
            
            if (reviewedQuestions.has(questionId)) {
                reviewedQuestions.delete(questionId);
                btn.innerHTML = '<i class="fas fa-check mr-1"></i> Mark Reviewed';
                btn.classList.remove('bg-green-500', 'text-white');
                btn.classList.add('bg-green-50', 'text-green-700');
                statusBadge.textContent = 'Pending';
                statusBadge.classList.remove('status-reviewed');
                statusBadge.classList.add('status-pending');
            } else {
                reviewedQuestions.add(questionId);
                btn.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Reviewed';
                btn.classList.remove('bg-green-50', 'text-green-700');
                btn.classList.add('bg-green-500', 'text-white');
                statusBadge.textContent = 'Reviewed';
                statusBadge.classList.remove('status-pending');
                statusBadge.classList.add('status-reviewed');
                logReview(questionId);
            }
            updateReviewedCount();
        }
        
        function updateReviewedCount() {
            document.getElementById('reviewedCount').textContent = reviewedQuestions.size;
        }
        
        function logReview(questionId) {
            const [section, index] = questionId.split('-');
            fetch('{{ route("dean.log.review") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    exam_id: '{{ $exam["id"] }}',
                    section: section,
                    questionIndex: parseInt(index),
                    type: 'review'
                })
            }).catch(err => console.error('Failed to log review:', err));
        }
        
        function toggleFeedback(questionId) {
            const panel = document.getElementById('feedback-' + questionId);
            panel.classList.toggle('hidden');
        }
        
        async function submitFeedback(section, questionIndex, questionId) {
            const input = document.getElementById('comment-input-' + questionId);
            const comment = input.value.trim();
            
            if (!comment) {
                showToast('Please enter a comment', 'warning');
                return;
            }
            
            try {
                const response = await fetch('{{ route("dean.add.comment") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        exam_id: '{{ $exam["id"] }}',
                        section: section,
                        questionIndex: questionIndex,
                        comment: comment,
                        type: 'general'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Comment added!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(data.error || 'Failed to add comment', 'error');
                }
            } catch (error) {
                showToast('Network error', 'error');
            }
        }
        
        function openDeclineModal() {
            document.getElementById('declineModal').classList.remove('hidden');
        }
        
        function closeDeclineModal() {
            document.getElementById('declineModal').classList.add('hidden');
        }
        
        // Save question with edit reason
        function saveQuestion(questionId) {
            const questionItem = document.getElementById('question-' + questionId);
            const form = questionItem.querySelector('.dean-edit-form');
            const reasonInput = questionItem.querySelector('input[name="edit_reason"]');
            
            if (!reasonInput || !reasonInput.value.trim()) {
                showToast('Please provide a reason for your edit', 'warning');
                if (reasonInput) reasonInput.focus();
                return;
            }
            
            // Create hidden input for edit reason inside form
            let hiddenReason = form.querySelector('input[name="edit_reason"][type="hidden"]');
            if (!hiddenReason) {
                hiddenReason = document.createElement('input');
                hiddenReason.type = 'hidden';
                hiddenReason.name = 'edit_reason';
                form.appendChild(hiddenReason);
            }
            hiddenReason.value = reasonInput.value;
            
            form.submit();
        }
        
        document.querySelectorAll('.dean-edit-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const reasonInput = form.querySelector('input[name="edit_reason"]');
                if (!reasonInput.value.trim()) {
                    e.preventDefault();
                    showToast('Please provide a reason for your edit', 'warning');
                    reasonInput.focus();
                }
            });
        });
        
        function showToast(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            const existing = document.querySelector('.toast-notification');
            if (existing) existing.remove();
            
            const toast = document.createElement('div');
            toast.className = 'toast-notification ' + colors[type];
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeclineModal();
        });
        
        document.getElementById('declineModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeclineModal();
        });
    </script>

</body>

</html>
