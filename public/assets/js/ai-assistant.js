/**
 * AI Writing Assistant for Exam Questions
 * Simple, user-friendly interface for lecturers
 */

window.AIAssistant = {
    currentEditor: null,
    isProcessing: false,
    modalLoaded: false,
};

/**
 * Load the AI Assistant Modal
 */
function loadAIAssistantModal() {
    if (window.AIAssistant.modalLoaded) return;

    const modalHTML = `
    <!-- AI Writing Assistant Modal -->
    <div id="aiAssistantModal" class="fixed inset-0 z-[9999] hidden">
        <div class="absolute inset-0 bg-black bg-opacity-40" onclick="hideAIAssistantModal()"></div>
        
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="relative bg-white w-full max-w-md rounded-lg shadow-xl pointer-events-auto overflow-hidden">
                
                <!-- Header - Compact -->
                <div class="flex items-center justify-between px-4 py-3 bg-blue-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-pen-fancy text-white"></i>
                        <h2 class="text-base font-semibold text-white">Writing Assistant</h2>
                    </div>
                    <button type="button" onclick="hideAIAssistantModal()" 
                            class="text-white hover:bg-blue-700 rounded p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Main Content -->
                <div class="p-4">
                    
                    <!-- Quick Tools - Simple Grid -->
                    <div id="aiQuickActions">
                        <p class="text-sm text-gray-600 mb-3">What would you like to do?</p>
                        
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <!-- Clean Up -->
                            <button onclick="executeAIAction('format')" 
                                    class="ai-action-btn flex items-center p-3 bg-gray-50 hover:bg-blue-50 rounded-lg border border-gray-200 hover:border-blue-300 transition-all text-left">
                                <i class="fas fa-broom text-blue-600 mr-2"></i>
                                <div>
                                    <div class="font-medium text-gray-800 text-sm">Clean Up</div>
                                    <div class="text-xs text-gray-500">Fix spacing & layout</div>
                                </div>
                            </button>

                            <!-- Check Grammar -->
                            <button onclick="executeAIAction('enhance')" 
                                    class="ai-action-btn flex items-center p-3 bg-gray-50 hover:bg-green-50 rounded-lg border border-gray-200 hover:border-green-300 transition-all text-left">
                                <i class="fas fa-spell-check text-green-600 mr-2"></i>
                                <div>
                                    <div class="font-medium text-gray-800 text-sm">Check Grammar</div>
                                    <div class="text-xs text-gray-500">Fix errors & clarity</div>
                                </div>
                            </button>

                            <!-- Fix Equations -->
                            <button onclick="executeAIAction('equation')" 
                                    class="ai-action-btn flex items-center p-3 bg-gray-50 hover:bg-purple-50 rounded-lg border border-gray-200 hover:border-purple-300 transition-all text-left">
                                <i class="fas fa-superscript text-purple-600 mr-2"></i>
                                <div>
                                    <div class="font-medium text-gray-800 text-sm">Fix Maths</div>
                                    <div class="text-xs text-gray-500">x^2 → x², √, π, Ω</div>
                                </div>
                            </button>

                            <!-- Number Questions -->
                            <button onclick="executeAIAction('structure')" 
                                    class="ai-action-btn flex items-center p-3 bg-gray-50 hover:bg-orange-50 rounded-lg border border-gray-200 hover:border-orange-300 transition-all text-left">
                                <i class="fas fa-list-ol text-orange-600 mr-2"></i>
                                <div>
                                    <div class="font-medium text-gray-800 text-sm">Add Numbers</div>
                                    <div class="text-xs text-gray-500">(a), (b), (c) format</div>
                                </div>
                            </button>
                        </div>

                        <!-- Custom Request - Collapsible -->
                        <div class="border-t pt-3">
                            <button onclick="toggleCustomInput()" id="customToggleBtn"
                                    class="w-full flex items-center justify-between p-2 text-sm text-gray-600 hover:bg-gray-50 rounded">
                                <span><i class="fas fa-comment-dots mr-2"></i>Tell AI what to do...</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" id="customChevron"></i>
                            </button>
                            
                            <div id="aiCustomInput" class="hidden mt-2">
                                <textarea id="aiCustomInstruction" rows="2" 
                                          class="w-full p-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                          placeholder="E.g., Add marks to each part, Make it simpler, Add more detail..."></textarea>
                                <button onclick="executeAIAction('custom')" 
                                        class="mt-2 w-full py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-paper-plane mr-1"></i> Send to AI
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Processing State -->
                    <div id="aiProcessing" class="hidden py-8 text-center">
                        <i class="fas fa-circle-notch fa-spin text-blue-600 text-2xl mb-3"></i>
                        <p class="text-gray-600">Working on it...</p>
                        <p class="text-xs text-gray-400 mt-1">This takes a few seconds</p>
                    </div>

                    <!-- Preview Section -->
                    <div id="aiPreview" class="hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                <i class="fas fa-eye mr-1 text-blue-600"></i> Preview
                            </span>
                            <button onclick="togglePreviewView()" class="text-xs text-blue-600 hover:underline">
                                Show code
                            </button>
                        </div>
                        <div class="border rounded-lg overflow-hidden">
                            <div id="aiPreviewContent" class="p-3 max-h-48 overflow-y-auto bg-gray-50 text-sm">
                            </div>
                            <div id="aiPreviewHTML" class="hidden p-3 max-h-48 overflow-y-auto bg-gray-800">
                                <pre class="text-xs text-green-400 whitespace-pre-wrap"><code id="aiPreviewCode"></code></pre>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex space-x-2 mt-3">
                            <button onclick="resetAIModal()" 
                                    class="flex-1 py-2 text-sm text-gray-600 bg-gray-100 rounded hover:bg-gray-200">
                                <i class="fas fa-undo mr-1"></i> Try Again
                            </button>
                            <button onclick="applyAIContent()" 
                                    class="flex-1 py-2 text-sm text-white bg-green-600 rounded hover:bg-green-700">
                                <i class="fas fa-check mr-1"></i> Use This
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Help Footer -->
                <div id="aiHelpFooter" class="px-4 py-2 bg-gray-50 border-t text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Type your question first, then click a tool above.
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="aiToast" class="fixed bottom-4 right-4 z-[10000] hidden">
        <div class="bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2 text-sm">
            <i id="aiToastIcon" class="fas fa-check-circle text-green-400"></i>
            <span id="aiToastMessage">Done!</span>
        </div>
    </div>
    `;

    document.body.insertAdjacentHTML("beforeend", modalHTML);
    window.AIAssistant.modalLoaded = true;
}

/**
 * Show Modal
 */
function showAIAssistantModal(editorSelector) {
    loadAIAssistantModal();
    window.AIAssistant.currentEditor = editorSelector;
    $(editorSelector).summernote("saveRange");
    resetAIModal();
    document.getElementById("aiAssistantModal").classList.remove("hidden");
    document.body.style.overflow = "hidden";
}

/**
 * Hide Modal
 */
function hideAIAssistantModal() {
    document.getElementById("aiAssistantModal").classList.add("hidden");
    document.body.style.overflow = "";
    window.AIAssistant.isProcessing = false;
}

/**
 * Reset modal
 */
function resetAIModal() {
    document.getElementById("aiQuickActions").classList.remove("hidden");
    document.getElementById("aiCustomInput").classList.add("hidden");
    document.getElementById("aiProcessing").classList.add("hidden");
    document.getElementById("aiPreview").classList.add("hidden");
    document.getElementById("aiHelpFooter").classList.remove("hidden");

    const chevron = document.getElementById("customChevron");
    if (chevron) chevron.classList.remove("rotate-180");

    document.querySelectorAll(".ai-action-btn").forEach((btn) => {
        btn.disabled = false;
        btn.classList.remove("opacity-50");
    });
}

/**
 * Toggle custom input
 */
function toggleCustomInput() {
    const input = document.getElementById("aiCustomInput");
    const chevron = document.getElementById("customChevron");
    input.classList.toggle("hidden");
    chevron.classList.toggle("rotate-180");
    if (!input.classList.contains("hidden")) {
        document.getElementById("aiCustomInstruction").focus();
    }
}

/**
 * Toggle preview view
 */
function togglePreviewView() {
    document.getElementById("aiPreviewContent").classList.toggle("hidden");
    document.getElementById("aiPreviewHTML").classList.toggle("hidden");
}

/**
 * Execute AI Action
 */
async function executeAIAction(action) {
    if (window.AIAssistant.isProcessing) return;

    const editorSelector = window.AIAssistant.currentEditor;
    if (!editorSelector) {
        showAIToast("Please select an editor first", "error");
        return;
    }

    const content = $(editorSelector).summernote("code");

    if (!content || content.trim() === "" || content === "<p><br></p>") {
        showAIToast("Please type something first", "warning");
        return;
    }

    let instruction = null;
    if (action === "custom") {
        instruction = document
            .getElementById("aiCustomInstruction")
            .value.trim();
        if (!instruction) {
            showAIToast("Please type your instruction", "warning");
            return;
        }
    }

    // Show processing
    window.AIAssistant.isProcessing = true;
    document.getElementById("aiQuickActions").classList.add("hidden");
    document.getElementById("aiHelpFooter").classList.add("hidden");
    document.getElementById("aiProcessing").classList.remove("hidden");

    try {
        const response = await fetch("/ai-assistant/process", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
            body: JSON.stringify({ content, action, instruction }),
        });

        const data = await response.json();

        if (data.success) {
            window.AIAssistant.processedContent = data.content;
            document.getElementById("aiProcessing").classList.add("hidden");
            document.getElementById("aiPreview").classList.remove("hidden");
            document.getElementById("aiPreviewContent").innerHTML =
                data.content;
            document.getElementById("aiPreviewCode").textContent = data.content;
            showAIToast("Ready! Check the preview below.", "success");
        } else {
            throw new Error(data.error || "Something went wrong");
        }
    } catch (error) {
        console.error("AI Assistant Error:", error);
        showAIToast(error.message || "Could not connect to AI", "error");
        resetAIModal();
    }

    window.AIAssistant.isProcessing = false;
}

/**
 * Apply content
 */
function applyAIContent() {
    const editorSelector = window.AIAssistant.currentEditor;
    const processedContent = window.AIAssistant.processedContent;

    if (!editorSelector || !processedContent) {
        showAIToast("Nothing to apply", "error");
        return;
    }

    $(editorSelector).summernote("code", processedContent);
    $(editorSelector).trigger("summernote.change");
    showAIToast("Changes applied!", "success");
    hideAIAssistantModal();
}

/**
 * Toast notification
 */
function showAIToast(message, type = "success") {
    const toast = document.getElementById("aiToast");
    const icon = document.getElementById("aiToastIcon");
    const messageEl = document.getElementById("aiToastMessage");

    const icons = {
        success: "fas fa-check-circle text-green-400",
        error: "fas fa-times-circle text-red-400",
        warning: "fas fa-exclamation-circle text-yellow-400",
    };

    icon.className = icons[type] || icons.success;
    messageEl.textContent = message;
    toast.classList.remove("hidden");

    setTimeout(() => toast.classList.add("hidden"), 3000);
}

/**
 * Summernote button
 */
function createAIAssistantButton(context) {
    const ui = $.summernote.ui;
    return ui
        .button({
            contents:
                '<i class="fas fa-pen-fancy"></i> <span class="hidden sm:inline">AI Help</span>',
            tooltip:
                "Writing Assistant - Clean up, check grammar, fix equations",
            container: false,
            click: function () {
                const $editor = context.$note;
                const editorId = $editor.attr("id");
                showAIAssistantModal(editorId ? "#" + editorId : ".summernote");
            },
        })
        .render();
}

$(document).ready(function () {
    if ($.summernote) {
        $.summernote.options.buttons = $.summernote.options.buttons || {};
        $.summernote.options.buttons.aiAssistant = createAIAssistantButton;
    }
});

window.showAIAssistantModal = showAIAssistantModal;
window.hideAIAssistantModal = hideAIAssistantModal;
window.executeAIAction = executeAIAction;
window.applyAIContent = applyAIContent;
window.toggleCustomInput = toggleCustomInput;
