// ========== GERENCIADOR DE ACESSIBILIDADE ==========

class AccessibilityManager {
  constructor() {
    this.panelOpen = false;
    this.settings = this.loadSettings();
    this.init();
  }

  init() {
    this.setupEventListeners();
    this.applySettings();
    this.setupScrollTracking();
  }

  setupScrollTracking() {
    // Rastrear scroll da página para manter botões visíveis
    window.addEventListener('scroll', () => {
      this.updateButtonPosition();
    }, { passive: true });
  }

  updateButtonPosition() {
    const button = document.querySelector('.accessibility-button');
    const panel = document.querySelector('.accessibility-panel');
    const librasButton = document.querySelector('.libras-button');

    if (button) {
      // Sempre manter o botão na viewport (position: fixed funciona aqui)
      // O position: fixed já mantém na viewport, então não precisa de ajuste
      // Apenas garantir que está sempre visível
      button.style.visibility = 'visible';
    }
  }

  setupEventListeners() {
    // Botão de acessibilidade
    const accessibilityBtn = document.querySelector('.accessibility-button');
    if (accessibilityBtn) {
      accessibilityBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        this.togglePanel();
      });
    }

    // Fechar painel
    const closeBtn = document.querySelector('.accessibility-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.closePanel());
    }

    // Overlay para fechar painel
    const overlay = document.querySelector('.accessibility-overlay');
    if (overlay) {
      overlay.addEventListener('click', () => this.closePanel());
    }

    // Controle de tamanho de texto
    const sizeSlider = document.querySelector('.size-slider');
    if (sizeSlider) {
      sizeSlider.addEventListener('input', (e) => {
        this.changeFontSize(parseInt(e.target.value));
      });
    }

    // Controle de contraste WCAG
    const contrastLevel = document.querySelector('#contrast-level');
    if (contrastLevel) {
      contrastLevel.addEventListener('change', (e) => {
        this.handleContrastChange(e.target.value);
      });
    }

    // Opções de checkbox
    const options = document.querySelectorAll('.accessibility-option input[type="checkbox"]');
    options.forEach((option) => {
      option.addEventListener('change', (e) => {
        this.handleOptionChange(e.target);
      });
    });

    // Botão de reset
    const resetBtn = document.querySelector('.accessibility-reset-btn');
    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        if (confirm('Deseja restaurar todas as configurações padrão de acessibilidade?')) {
          this.resetSettings();
        }
      });
    }

    // Fechar painel ao clicar fora
    document.addEventListener('click', (e) => {
      const panel = document.querySelector('.accessibility-panel');
      const accessibilityBtn = document.querySelector('.accessibility-button');
      
      if (this.panelOpen && 
          !panel.contains(e.target) && 
          !accessibilityBtn.contains(e.target)) {
        this.closePanel();
      }
    });
  }

  togglePanel() {
    if (this.panelOpen) {
      this.closePanel();
    } else {
      this.openPanel();
    }
  }

  openPanel() {
    const panel = document.querySelector('.accessibility-panel');
    const overlay = document.querySelector('.accessibility-overlay');
    const button = document.querySelector('.accessibility-button');

    console.log('openPanel called');
    console.log('Panel element:', panel);
    console.log('Panel classes before:', panel?.className);

    if (panel) {
      panel.classList.add('active');
      // Forçar display com inline style como fallback
      panel.style.display = 'flex';
      console.log('Panel classes after:', panel.className);
      console.log('Panel display style:', panel.style.display);
      this.panelOpen = true;
    }

    if (overlay) {
      overlay.classList.add('active');
      overlay.style.display = 'block';
    }

    if (button) {
      button.classList.add('active');
    }
    
    console.log('openPanel finished, panelOpen:', this.panelOpen);
  }

  closePanel() {
    const panel = document.querySelector('.accessibility-panel');
    const overlay = document.querySelector('.accessibility-overlay');
    const button = document.querySelector('.accessibility-button');

    console.log('closePanel called');

    if (panel) {
      panel.classList.remove('active');
      // Forçar display com inline style como fallback
      panel.style.display = 'none';
      this.panelOpen = false;
    }

    if (overlay) {
      overlay.classList.remove('active');
      overlay.style.display = 'none';
    }

    if (button) {
      button.classList.remove('active');
    }
    
    console.log('closePanel finished, panelOpen:', this.panelOpen);
  }

  changeFontSize(percentage) {
    const value = (percentage - 100) / 50; // Transforma 50-150 para -1 a 1
    const multiplier = 1 + value * 0.5; // 0.5x a 1.5x

    document.documentElement.style.fontSize = (16 * multiplier) + 'px';

    this.settings.fontSize = percentage;
    this.saveSettings();

    // Atualizar valor do slider
    const valueDisplay = document.querySelector('.size-value');
    if (valueDisplay) {
      valueDisplay.textContent = percentage + '%';
    }
  }

  handleContrastChange(level) {
    // Remove todas as classes de contraste
    document.body.classList.remove('wcag-aa', 'wcag-aaa');
    
    // Adiciona a classe apropriada
    if (level === 'wcag-aa') {
      document.body.classList.add('wcag-aa');
    } else if (level === 'wcag-aaa') {
      document.body.classList.add('wcag-aaa');
    }
    
    this.settings['contrast-level'] = level;
    this.saveSettings();
  }

  handleOptionChange(target) {
    const optionId = target.id;
    const isChecked = target.checked;

    switch (optionId) {
      case 'sans-serif':
        this.toggleSansSerif(isChecked);
        break;
      case 'dyslexia-font':
        this.toggleDyslexiaFont(isChecked);
        break;
      case 'increased-spacing':
        this.toggleIncreaedSpacing(isChecked);
        break;
      case 'inverted-mode':
        this.toggleInvertedMode(isChecked);
        break;
      case 'reading-guide':
        this.toggleReadingGuide(isChecked);
        break;
      case 'monospace-font':
        this.toggleMonospaceFont(isChecked);
        break;
      case 'expanded-focus':
        this.toggleExpandedFocus(isChecked);
        break;
      case 'large-cursor':
        this.toggleLargeCursor(isChecked);
        break;
    }

    this.settings[optionId] = isChecked;
    this.saveSettings();
  }

  handleContrastChange(level) {
    // Remove todas as classes de contraste
    document.body.classList.remove('wcag-aa', 'wcag-aaa');
    
    // Adiciona a classe apropriada
    if (level === 'wcag-aa') {
      document.body.classList.add('wcag-aa');
    } else if (level === 'wcag-aaa') {
      document.body.classList.add('wcag-aaa');
    }
    
    this.settings['contrast-level'] = level;
    this.saveSettings();
  }

  toggleSansSerif(enabled) {
    if (enabled) {
      document.body.classList.add('sans-serif-font');
    } else {
      document.body.classList.remove('sans-serif-font');
    }
  }

  toggleDyslexiaFont(enabled) {
    if (enabled) {
      document.body.classList.add('dyslexia-font');
    } else {
      document.body.classList.remove('dyslexia-font');
    }
  }

  toggleIncreaedSpacing(enabled) {
    if (enabled) {
      document.body.classList.add('increased-spacing');
    } else {
      document.body.classList.remove('increased-spacing');
    }
  }

  toggleInvertedMode(enabled) {
    if (enabled) {
      document.body.classList.add('inverted-mode');
    } else {
      document.body.classList.remove('inverted-mode');
    }
  }

  toggleReadingGuide(enabled) {
    if (enabled) {
      document.body.classList.add('reading-guide');
    } else {
      document.body.classList.remove('reading-guide');
    }
  }

  toggleMonospaceFont(enabled) {
    if (enabled) {
      document.body.classList.add('monospace-font');
    } else {
      document.body.classList.remove('monospace-font');
    }
  }

  toggleExpandedFocus(enabled) {
    if (enabled) {
      document.body.classList.add('expanded-focus');
    } else {
      document.body.classList.remove('expanded-focus');
    }
  }

  toggleLargeCursor(enabled) {
    if (enabled) {
      document.body.classList.add('large-cursor');
    } else {
      document.body.classList.remove('large-cursor');
    }
  }

  applySettings() {
    // Aplicar tamanho de fonte
    if (this.settings.fontSize) {
      this.changeFontSize(this.settings.fontSize);
    }

    // Aplicar nível de contraste
    if (this.settings['contrast-level'] && this.settings['contrast-level'] !== 'none') {
      const contrastSelect = document.querySelector('#contrast-level');
      if (contrastSelect) {
        contrastSelect.value = this.settings['contrast-level'];
        this.handleContrastChange(this.settings['contrast-level']);
      }
    }

    // Aplicar outros settings
    if (this.settings['sans-serif']) {
      this.toggleSansSerif(true);
      const checkbox = document.querySelector('#sans-serif');
      if (checkbox) checkbox.checked = true;
    }

    if (this.settings['dyslexia-font']) {
      this.toggleDyslexiaFont(true);
      const checkbox = document.querySelector('#dyslexia-font');
      if (checkbox) checkbox.checked = true;
    }

    if (this.settings['increased-spacing']) {
      this.toggleIncreaedSpacing(true);
      const checkbox = document.querySelector('#increased-spacing');
      if (checkbox) checkbox.checked = true;
    }

    if (this.settings['inverted-mode']) {
      this.toggleInvertedMode(true);
      const checkbox = document.querySelector('#inverted-mode');
      if (checkbox) checkbox.checked = true;
    }

    if (this.settings['reading-guide']) {
      this.toggleReadingGuide(true);
      const checkbox = document.querySelector('#reading-guide');
      if (checkbox) checkbox.checked = true;
    }

    if (this.settings['monospace-font']) {
      this.toggleMonospaceFont(true);
      const checkbox = document.querySelector('#monospace-font');
      if (checkbox) checkbox.checked = true;
    }

    if (this.settings['expanded-focus']) {
      this.toggleExpandedFocus(true);
      const checkbox = document.querySelector('#expanded-focus');
      if (checkbox) checkbox.checked = true;
    }

    if (this.settings['large-cursor']) {
      this.toggleLargeCursor(true);
      const checkbox = document.querySelector('#large-cursor');
      if (checkbox) checkbox.checked = true;
    }
  }

  resetSettings() {
    // Remover todas as classes
    document.body.classList.remove(
      'wcag-aa',
      'wcag-aaa',
      'sans-serif-font',
      'dyslexia-font',
      'increased-spacing',
      'inverted-mode',
      'reading-guide',
      'monospace-font',
      'expanded-focus',
      'large-cursor'
    );

    // Reset font size
    document.documentElement.style.fontSize = '16px';

    // Reset select de contraste
    const contrastSelect = document.querySelector('#contrast-level');
    if (contrastSelect) {
      contrastSelect.value = 'none';
    }

    // Desmarca todos os checkboxes
    document.querySelectorAll('.accessibility-option input[type="checkbox"]').forEach(checkbox => {
      checkbox.checked = false;
    });

    // Reset slider
    const slider = document.querySelector('.size-slider');
    if (slider) {
      slider.value = 100;
      const valueDisplay = document.querySelector('.size-value');
      if (valueDisplay) {
        valueDisplay.textContent = '100%';
      }
    }

    this.settings = {
      fontSize: 100,
      'contrast-level': 'none',
      'sans-serif': false,
      'dyslexia-font': false,
      'increased-spacing': false,
      'inverted-mode': false,
      'reading-guide': false,
      'monospace-font': false,
      'expanded-focus': false,
      'large-cursor': false
    };

    this.saveSettings();

    // Animação de reset
    const btn = document.querySelector('.accessibility-button');
    if (btn) {
      btn.classList.add('pulse');
      setTimeout(() => btn.classList.remove('pulse'), 600);
    }
  }

  saveSettings() {
    localStorage.setItem('accessibilitySettings', JSON.stringify(this.settings));
  }

  loadSettings() {
    const saved = localStorage.getItem('accessibilitySettings');
    return saved ? JSON.parse(saved) : {
      fontSize: 100,
      'contrast-level': 'none',
      'sans-serif': false,
      'dyslexia-font': false,
      'increased-spacing': false,
      'inverted-mode': false,
      'reading-guide': false,
      'monospace-font': false,
      'expanded-focus': false,
      'large-cursor': false
    };
  }
}

// Função global de toggle para compatibilidade com onclick
function toggleAccessibility(event) {
  if (event) {
    event.stopPropagation();
  }
  console.log('toggleAccessibility called, manager:', window.accessibilityManager);
  
  // Espera até que o manager esteja disponível
  if (!window.accessibilityManager) {
    console.warn('AccessibilityManager not initialized yet');
    return;
  }
  console.log('Toggling panel...');
  window.accessibilityManager.togglePanel();
}

// Tornar função explicitamente global
window.toggleAccessibility = toggleAccessibility;

// Inicializar ao carregar a página
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded: Initializing AccessibilityManager');
    window.accessibilityManager = new AccessibilityManager();
  });
} else {
  // Se o documento já foi carregado, inicializar imediatamente
  console.log('Document already loaded: Initializing AccessibilityManager');
  window.accessibilityManager = new AccessibilityManager();
}
