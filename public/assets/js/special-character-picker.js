function loadSpecialCharModal() {
  const modalHTML = `
    <!-- Tailwind-based Special Character Picker Modal -->
    <div id="specialCharModal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <!-- Modal Container -->
      <div class="relative bg-white w-full max-w-4xl rounded-md shadow-lg">

        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-3">
          <h2 class="text-lg font-semibold">Insert Special Character</h2>
          <!-- Close Button -->
          <button type="button"
                  class="text-gray-400 hover:text-gray-600 text-2xl font-bold"
                  onclick="hideSpecialCharModal()">
            &times;
          </button>
        </div>

        <!-- Tabs -->
        <div class="px-6 border-b border-gray-200">
          <ul id="specialCharTabs" class="flex flex-wrap text-sm font-medium text-gray-500 space-x-4">
            <li>
              <button class="py-2 border-b-2 border-transparent active-tab"
                      data-target="tab-general">General</button>
            </li>
            <li>
              <button class="py-2 border-b-2 border-transparent"
                      data-target="tab-greek">Greek Letters</button>
            </li>
            <li>
              <button class="py-2 border-b-2 border-transparent"
                      data-target="tab-operators">Math Operators</button>
            </li>
            <li>
              <button class="py-2 border-b-2 border-transparent"
                      data-target="tab-electrical">Electrical</button>
            </li>
            <li>
              <button class="py-2 border-b-2 border-transparent"
                      data-target="tab-mechanical">Mechanical/Civil</button>
            </li>
            <li>
              <button class="py-2 border-b-2 border-transparent"
                      data-target="tab-units">Units & Constants</button>
            </li>
            <li>
              <button class="py-2 border-b-2 border-transparent"
                      data-target="tab-arrows">Arrows</button>
            </li>
            <li>
              <button class="py-2 border-b-2 border-transparent"
                      data-target="tab-set">Set Notation</button>
            </li>
            <li>
              <button class="py-2 border-b-2 border-transparent"
                      data-target="tab-calculus">Calculus</button>
            </li>
            <li>
              <button class="py-2 border-b-2 border-transparent"
                      data-target="tab-misc">Miscellaneous</button>
            </li>
          </ul>
        </div>

        <!-- Content Container -->
        <div class="px-6 py-4 max-h-96 overflow-y-auto">
          <!-- GENERAL -->
          <div id="tab-general" class="special-char-content">
            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
              <span class="special-char-box">∑</span>
              <span class="special-char-box">∫</span>
              <span class="special-char-box">√</span>
              <span class="special-char-box">≈</span>
              <span class="special-char-box">≠</span>
              <span class="special-char-box">∞</span>
              <span class="special-char-box">∂</span>
              <span class="special-char-box">∇</span>
              <span class="special-char-box">π</span>
              <span class="special-char-box">θ</span>
              <span class="special-char-box">†</span>
              <span class="special-char-box">‡</span>
              <span class="special-char-box">§</span>
              <span class="special-char-box">¶</span>
              <span class="special-char-box">©</span>
              <span class="special-char-box">®</span>
              <span class="special-char-box">™</span>
              <span class="special-char-box">°</span>
              <span class="special-char-box">±</span>
              <span class="special-char-box">×</span>
              <span class="special-char-box">÷</span>
              <span class="special-char-box">′</span>
              <span class="special-char-box">″</span>
            </div>
          </div>

          <!-- GREEK -->
          <div id="tab-greek" class="special-char-content hidden">
            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
              <span class="special-char-box">α</span>
              <span class="special-char-box">β</span>
              <span class="special-char-box">γ</span>
              <span class="special-char-box">δ</span>
              <span class="special-char-box">ε</span>
              <span class="special-char-box">ζ</span>
              <span class="special-char-box">η</span>
              <span class="special-char-box">θ</span>
              <span class="special-char-box">ι</span>
              <span class="special-char-box">κ</span>
              <span class="special-char-box">λ</span>
              <span class="special-char-box">μ</span>
              <span class="special-char-box">ν</span>
              <span class="special-char-box">ξ</span>
              <span class="special-char-box">ο</span>
              <span class="special-char-box">π</span>
              <span class="special-char-box">ρ</span>
              <span class="special-char-box">σ</span>
              <span class="special-char-box">τ</span>
              <span class="special-char-box">υ</span>
              <span class="special-char-box">φ</span>
              <span class="special-char-box">χ</span>
              <span class="special-char-box">ψ</span>
              <span class="special-char-box">ω</span>
              <span class="special-char-box">Α</span>
              <span class="special-char-box">Β</span>
              <span class="special-char-box">Γ</span>
              <span class="special-char-box">Δ</span>
              <span class="special-char-box">Ε</span>
              <span class="special-char-box">Ζ</span>
              <span class="special-char-box">Η</span>
              <span class="special-char-box">Θ</span>
              <span class="special-char-box">Ι</span>
              <span class="special-char-box">Κ</span>
              <span class="special-char-box">Λ</span>
              <span class="special-char-box">Μ</span>
              <span class="special-char-box">Ν</span>
              <span class="special-char-box">Ξ</span>
              <span class="special-char-box">Ο</span>
              <span class="special-char-box">Π</span>
              <span class="special-char-box">Ρ</span>
              <span class="special-char-box">Σ</span>
              <span class="special-char-box">Τ</span>
              <span class="special-char-box">Υ</span>
              <span class="special-char-box">Φ</span>
              <span class="special-char-box">Χ</span>
              <span class="special-char-box">Ψ</span>
              <span class="special-char-box">Ω</span>
            </div>
          </div>

          <!-- MATH OPERATORS -->
          <div id="tab-operators" class="special-char-content hidden">
            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
              <span class="special-char-box">±</span>
              <span class="special-char-box">∓</span>
              <span class="special-char-box">×</span>
              <span class="special-char-box">÷</span>
              <span class="special-char-box">∕</span>
              <span class="special-char-box">∝</span>
              <span class="special-char-box">∑</span>
              <span class="special-char-box">∏</span>
              <span class="special-char-box">√</span>
              <span class="special-char-box">∛</span>
              <span class="special-char-box">∜</span>
              <span class="special-char-box">≤</span>
              <span class="special-char-box">≥</span>
              <span class="special-char-box">≪</span>
              <span class="special-char-box">≫</span>
              <span class="special-char-box">∠</span>
              <span class="special-char-box">∡</span>
              <span class="special-char-box">∢</span>
              <span class="special-char-box">≡</span>
              <span class="special-char-box">≅</span>
              <span class="special-char-box">≈</span>
              <span class="special-char-box">≠</span>
              <span class="special-char-box">≝</span>
              <span class="special-char-box">≜</span>
              <span class="special-char-box">≔</span>
              <span class="special-char-box">≕</span>
              <span class="special-char-box">∥</span>
              <span class="special-char-box">⊥</span>
              <span class="special-char-box">⊕</span>
              <span class="special-char-box">⊗</span>
              <span class="special-char-box">⊙</span>
              <span class="special-char-box">⨁</span>
              <span class="special-char-box">⨂</span>
            </div>
          </div>

          <!-- ELECTRICAL -->
          <div id="tab-electrical" class="special-char-content hidden">
            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
              <span class="special-char-box">Ω</span>
              <span class="special-char-box">µ</span>
              <span class="special-char-box">Φ</span>
              <span class="special-char-box">Θ</span>
              <span class="special-char-box">℧</span>
              <span class="special-char-box">⏦</span>
              <span class="special-char-box">⌁</span>
              <span class="special-char-box">⎓</span>
              <span class="special-char-box">⏚</span>
              <span class="special-char-box">⏗</span>
              <span class="special-char-box">⏛</span>
              <span class="special-char-box">△</span>
              <span class="special-char-box">⚡</span>
              <span class="special-char-box">↯</span>
              <span class="special-char-box">∿</span>
              <span class="special-char-box">⏧</span>
              <span class="special-char-box">∟</span>
              <span class="special-char-box">∠</span>
              <span class="special-char-box">⏍</span>
              <span class="special-char-box">∥</span>
              <span class="special-char-box">⊥</span>
              <span class="special-char-box">⏤</span>
              <span class="special-char-box">⏥</span>
            </div>
          </div>

          <!-- MECHANICAL -->
          <div id="tab-mechanical" class="special-char-content hidden">
            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
              <span class="special-char-box">⌀</span>
              <span class="special-char-box">∅</span>
              <span class="special-char-box">⊖</span>
              <span class="special-char-box">⊘</span>
              <span class="special-char-box">⦵</span>
              <span class="special-char-box">⦶</span>
              <span class="special-char-box">∟</span>
              <span class="special-char-box">∠</span>
              <span class="special-char-box">⊾</span>
              <span class="special-char-box">⊿</span>
              <span class="special-char-box">⊥</span>
              <span class="special-char-box">∥</span>
              <span class="special-char-box">⊓</span>
              <span class="special-char-box">⊔</span>
              <span class="special-char-box">△</span>
              <span class="special-char-box">▽</span>
              <span class="special-char-box">□</span>
              <span class="special-char-box">◊</span>
              <span class="special-char-box">○</span>
              <span class="special-char-box">⌂</span>
              <span class="special-char-box">⚪</span>
              <span class="special-char-box">⬣</span>
              <span class="special-char-box">⬢</span>
              <span class="special-char-box">⬠</span>
            </div>
          </div>

          <!-- UNITS -->
          <div id="tab-units" class="special-char-content hidden">
            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
              <span class="special-char-box">µ</span>
              <span class="special-char-box">Ω</span>
              <span class="special-char-box">℃</span>
              <span class="special-char-box">℉</span>
              <span class="special-char-box">K</span>
              <span class="special-char-box">°</span>
              <span class="special-char-box">′</span>
              <span class="special-char-box">″</span>
              <span class="special-char-box">‴</span>
              <span class="special-char-box">Å</span>
              <span class="special-char-box">℮</span>
              <span class="special-char-box">℧</span>
              <span class="special-char-box">ℏ</span>
              <span class="special-char-box">ℇ</span>
              <span class="special-char-box">ℑ</span>
              <span class="special-char-box">ℜ</span>
              <span class="special-char-box">℘</span>
              <span class="special-char-box">ℵ</span>
              <span class="special-char-box">Ψ</span>
              <span class="special-char-box">℞</span>
              <span class="special-char-box">№</span>
              <span class="special-char-box">™</span>
              <span class="special-char-box">℥</span>
              <span class="special-char-box">℔</span>
            </div>
          </div>

          <!-- ARROWS -->
          <div id="tab-arrows" class="special-char-content hidden">
            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
              <span class="special-char-box">←</span>
              <span class="special-char-box">→</span>
              <span class="special-char-box">↑</span>
              <span class="special-char-box">↓</span>
              <span class="special-char-box">⇐</span>
              <span class="special-char-box">⇒</span>
              <span class="special-char-box">⇑</span>
              <span class="special-char-box">⇓</span>
              <span class="special-char-box">↔</span>
              <span class="special-char-box">⇔</span>
              <span class="special-char-box">↩</span>
              <span class="special-char-box">↪</span>
              <span class="special-char-box">↻</span>
              <span class="special-char-box">↺</span>
              <span class="special-char-box">⇄</span>
              <span class="special-char-box">⇅</span>
              <span class="special-char-box">↷</span>
              <span class="special-char-box">↶</span>
              <span class="special-char-box">⟵</span>
              <span class="special-char-box">⟶</span>
              <span class="special-char-box">⟷</span>
              <span class="special-char-box">⟹</span>
              <span class="special-char-box">⟸</span>
              <span class="special-char-box">⟺</span>
              <span class="special-char-box">⟼</span>
              <span class="special-char-box">⟿</span>
              <span class="special-char-box">↫</span>
              <span class="special-char-box">↬</span>
              <span class="special-char-box">↭</span>
              <span class="special-char-box">↮</span>
              <span class="special-char-box">↯</span>
              <span class="special-char-box">↰</span>
            </div>
          </div>

          <!-- SET -->
          <div id="tab-set" class="special-char-content hidden">
            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
              <span class="special-char-box">∈</span>
              <span class="special-char-box">∉</span>
              <span class="special-char-box">⊆</span>
              <span class="special-char-box">⊇</span>
              <span class="special-char-box">⊂</span>
              <span class="special-char-box">⊃</span>
              <span class="special-char-box">∪</span>
              <span class="special-char-box">∩</span>
              <span class="special-char-box">∅</span>
              <span class="special-char-box">∄</span>
              <span class="special-char-box">∃</span>
              <span class="special-char-box">∀</span>
              <span class="special-char-box">∁</span>
              <span class="special-char-box">∖</span>
              <span class="special-char-box">∋</span>
              <span class="special-char-box">⊄</span>
              <span class="special-char-box">⊅</span>
              <span class="special-char-box">⊊</span>
              <span class="special-char-box">⊋</span>
              <span class="special-char-box">⊆</span>
              <span class="special-char-box">⊇</span>
              <span class="special-char-box">⊈</span>
              <span class="special-char-box">⊉</span>
              <span class="special-char-box">∌</span>
              <span class="special-char-box">∦</span>
              <span class="special-char-box">∣</span>
              <span class="special-char-box">∤</span>
              <span class="special-char-box">⋂</span>
              <span class="special-char-box">⋃</span>
            </div>
          </div>

          <!-- CALCULUS -->
          <div id="tab-calculus" class="special-char-content hidden">
            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
              <span class="special-char-box">∫</span>
              <span class="special-char-box">∬</span>
              <span class="special-char-box">∭</span>
              <span class="special-char-box">∮</span>
              <span class="special-char-box">∯</span>
              <span class="special-char-box">∰</span>
              <span class="special-char-box">∇</span>
              <span class="special-char-box">∂</span>
              <span class="special-char-box">∆</span>
              <span class="special-char-box">∑</span>
              <span class="special-char-box">∏</span>
              <span class="special-char-box">∐</span>
              <span class="special-char-box">→</span>
              <span class="special-char-box">lim</span>
              <span class="special-char-box">∞</span>
              <span class="special-char-box">↗</span>
              <span class="special-char-box">↘</span>
              <span class="special-char-box">δ</span>
              <span class="special-char-box">ε</span>
              <span class="special-char-box">∆</span>
              <span class="special-char-box">𝑑</span>
              <span class="special-char-box">ƒ′</span>
              <span class="special-char-box">ƒ″</span>
              <span class="special-char-box">ƒ‴</span>
            </div>
          </div>

          <!-- MISC -->
          <div id="tab-misc" class="special-char-content hidden">
            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
              <span class="special-char-box">⌊</span>
              <span class="special-char-box">⌋</span>
              <span class="special-char-box">⌈</span>
              <span class="special-char-box">⌉</span>
              <span class="special-char-box">≡</span>
              <span class="special-char-box">≅</span>
              <span class="special-char-box">≈</span>
              <span class="special-char-box">⊥</span>
              <span class="special-char-box">∥</span>
              <span class="special-char-box">♠</span>
              <span class="special-char-box">♥</span>
              <span class="special-char-box">♦</span>
              <span class="special-char-box">♣</span>
              <span class="special-char-box">♪</span>
              <span class="special-char-box">♫</span>
              <span class="special-char-box">☑</span>
              <span class="special-char-box">☒</span>
              <span class="special-char-box">☐</span>
              <span class="special-char-box">★</span>
              <span class="special-char-box">☆</span>
              <span class="special-char-box">⚠</span>
              <span class="special-char-box">⚙</span>
              <span class="special-char-box">⌨</span>
              <span class="special-char-box">⚓</span>
              <span class="special-char-box">⚛</span>
              <span class="special-char-box">⚘</span>
              <span class="special-char-box">⚔</span>
              <span class="special-char-box">⚒</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Inline Tailwind styles for the character boxes and active tab -->
    <style>
      .special-char-box {
        @apply border border-gray-300 rounded-md p-2 text-center 
               cursor-pointer hover:bg-gray-100 text-lg transition-colors duration-200;
      }
      .active-tab {
        @apply border-blue-500 text-blue-600 font-semibold;
      }
    </style>
  `;
  document.body.insertAdjacentHTML('beforeend', modalHTML);
  initSpecialCharTabs(); // Initialize tab switching
}

/************************************************************
 * SHOW / HIDE MODAL (Tailwind)
 ************************************************************/
function showSpecialCharModal() {
  document.getElementById('specialCharModal').classList.remove('hidden');
}
function hideSpecialCharModal() {
  document.getElementById('specialCharModal').classList.add('hidden');
}

/************************************************************
 * TAB SWITCHING LOGIC (Tailwind-based)
 ************************************************************/
function initSpecialCharTabs() {
  const tabs = document.querySelectorAll('#specialCharTabs button');
  const contents = document.querySelectorAll('.special-char-content');

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      // Remove "active" styles from all tabs
      tabs.forEach(t => t.classList.remove('border-blue-500', 'text-blue-600', 'active-tab'));
      // Hide all content
      contents.forEach(c => c.classList.add('hidden'));

      // Activate the clicked tab
      tab.classList.add('border-blue-500', 'text-blue-600', 'active-tab');
      document.getElementById(tab.dataset.target).classList.remove('hidden');
    });
  });
}

/************************************************************
 * INSERT CHARACTER INTO SUMMERNOTE WHEN BOX CLICKED
 ************************************************************/
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('special-char-box')) {
    const char = e.target.textContent;
    // Restore the Summernote cursor position
    $('.summernote').summernote('restoreRange');
    // Insert the character
    $('.summernote').summernote('editor.insertText', char);
    // Hide the modal
    hideSpecialCharModal();
  }
});
