{{-- Simple Client-side Translation Handler --}}
<script>
    /**
     * Simple translation utility for client-side use
     * Usage: await translateText('Selamat datang')
     */
    window.translateText = async function(text, target = 'en') {
        if (!text || text.trim() === '') {
            return text;
        }

        try {
            const response = await fetch('/api/translate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    text: text,
                    target: target
                })
            });

            if (!response.ok) {
                console.warn('Translation API error:', response.status);
                return text;
            }

            const data = await response.json();
            return data.data?.translated || text;
        } catch (error) {
            console.warn('Translation failed:', error);
            return text;
        }
    };

    /**
     * Translate multiple texts at once
     */
    window.translateTexts = async function(texts, target = 'en') {
        if (!Array.isArray(texts)) {
            return texts;
        }

        try {
            const response = await fetch('/api/translate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    texts: texts,
                    target: target
                })
            });

            if (!response.ok) {
                return texts;
            }

            const data = await response.json();
            return data.data?.map(item => item.translated) || texts;
        } catch (error) {
            console.warn('Translation failed:', error);
            return texts;
        }
    };

    /**
     * Get current locale from server
     */
    window.getCurrentLocale = function() {
        return localStorage.getItem('locale') || document.documentElement.lang || 'id';
    };

    /**
     * Check if page should be translated
     */
    window.shouldTranslate = function() {
        return window.getCurrentLocale() === 'en';
    };

    // Auto-translate page text when English is selected
    document.addEventListener('DOMContentLoaded', function() {
        if (window.shouldTranslate()) {
            console.log('Page locale is English - ready for translation via API');
            // Translation can be triggered manually via translateText() or through page refresh with ?lang=en
        }
    });
</script>

