#!/usr/bin/env python3
"""
Script to update interview/prep.blade.php with all UX improvements
"""

import re

# Read the file
with open('resources/views/user/interview/prep.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update renderQuestions function to add difficulty grouping and improved styling
old_renderquestions = '''        function renderQuestions() {
            const questionsList = document.getElementById('questionsList');
            const progressBar = document.getElementById('progressBar');
            const startIndex = 0;
            const endIndex = (currentPage + 1) * questionsPerPage;
            const visibleQuestions = allQuestions.slice(startIndex, endIndex);

            let html = '';
            visibleQuestions.forEach((q, index) => {
                const absoluteIndex = index;
                const uniqueId = `answer-${absoluteIndex}`;
                const difficulty = q.difficulty || 'medium';
                const difficultyColors = {
                    'easy': { badge: 'bg-success', label: 'Easy', tooltip: 'Common question' },
                    'medium': { badge: 'bg-warning', label: 'Medium', tooltip: 'Frequently asked' },
                    'hard': { badge: 'bg-danger', label: 'Hard', tooltip: 'High-impact question' }
                };
                const diffStyle = difficultyColors[difficulty] || difficultyColors['medium'];

                html += `
                    <div class="question-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                <div class="question-number">${absoluteIndex + 1}</div>
                            </div>
                            <span class="badge ${diffStyle.badge} text-white small" title="${diffStyle.tooltip}">${diffStyle.label}</span>
                        </div>
                        <h6 class="mb-2 fw-bold">${escapeHtml(q.question)}</h6>
                        ${q.sample_answer ? `
                            <div class="answer-preview text-muted small">💡 Sample answer available</div>
                            <button class="btn btn-link btn-sm p-0 text-decoration-none" onclick="toggleAnswer('${uniqueId}')" style="color: #667eea; font-size: 0.85rem;">
                                <i class="bx bx-chevron-down me-1" id="${uniqueId}-icon" style="vertical-align:-2px; font-size: 0.9rem; transition: transform 0.2s ease;"></i>
                                <span id="${uniqueId}-text">Show answer</span>
                            </button>
                            <div class="answer-box" id="${uniqueId}">
                                <strong class="d-block mb-1" style="color:#16a34a;">
                                    <i class="bx bx-bulb me-1"></i>Sample Answer
                                </strong>
                                <p class="mb-0 small" style="line-height:1.5;">${escapeHtml(q.sample_answer)}</p>
                            </div>
                        ` : ''}
                        ${q.tips && q.tips.length > 0 ? `
                            <button class="btn btn-link btn-sm p-0 text-decoration-none mt-2" onclick="toggleTips('${uniqueId}-tips')" style="color: #6b7280; font-size: 0.85rem;">
                                <i class="bx bx-bulb me-1" style="vertical-align:-1px;"></i>
                                <span>Tips</span>
                            </button>
                            <div class="tips-box" id="${uniqueId}-tips" style="display: none;">
                                <ul class="small mb-0">
                                    ${q.tips.map(tip => `<li>${escapeHtml(tip)}</li>`).join('')}
                                </ul>
                            </div>
                        ` : ''}
                    </div>
                `;

                // Add secondary CTA every 3 questions
                if ((absoluteIndex + 1) % 3 === 0 && absoluteIndex + 1 < visibleQuestions.length) {
                    html += `
                        <div class="text-center my-3">
                            <a href="{{ route('user.pricing') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-crown me-1"></i> Get AI Feedback on Your Answers
                            </a>
                        </div>
                    `;
                }
            });

            // Add load more button if there are more questions
            if (endIndex < allQuestions.length) {
                html += `
                    <div class="text-center mt-3 mb-4">
                        <button class="btn btn-outline-primary btn-sm" onclick="loadMoreQuestions()" id="loadMoreBtn">
                            <i class="bx bx-plus me-1"></i> Load more questions (${allQuestions.length - endIndex} remaining)
                        </button>
                    </div>
                `;
            }

            questionsList.innerHTML = html;

            // Update progress bar
            const totalVisible = visibleQuestions.length;
            const totalQuestions = allQuestions.length;
            const progressPercent = (totalVisible / totalQuestions) * 100;
            if (progressBar) progressBar.style.width = progressPercent + '%';
        }'''

new_renderquestions = '''        function renderQuestions() {
            const questionsList = document.getElementById('questionsList');
            const progressBar = document.getElementById('progressBar');
            const startIndex = 0;
            const endIndex = (currentPage + 1) * questionsPerPage;
            const visibleQuestions = allQuestions.slice(startIndex, endIndex);

            let html = '';
            let lastDifficulty = null;
            let questionCount = 0;
            
            visibleQuestions.forEach((q, index) => {
                const absoluteIndex = index;
                const uniqueId = `answer-${absoluteIndex}`;
                const difficulty = q.difficulty || 'medium';
                const difficultyColors = {
                    'easy': { badge: 'badge-success', label: 'Easy', tooltip: 'Common question', section: 'Easy Questions' },
                    'medium': { badge: 'badge-warning', label: 'Medium', tooltip: 'Frequently asked', section: 'Medium Questions' },
                    'hard': { badge: 'badge-danger', label: 'Hard', tooltip: 'High-impact question', section: 'Hard Questions' }
                };
                const diffStyle = difficultyColors[difficulty] || difficultyColors['medium'];

                // Add section header when difficulty changes
                if (lastDifficulty !== difficulty) {
                    html += `<div class="question-section-header">${diffStyle.section}</div>`;
                    lastDifficulty = difficulty;
                }

                html += `
                    <div class="question-card">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="d-flex align-items-center">
                                <div class="question-number">${absoluteIndex + 1}</div>
                            </div>
                            <span class="badge ${diffStyle.badge}" title="${diffStyle.tooltip}">${diffStyle.label}</span>
                        </div>
                        <h6 class="question-text mb-2">${escapeHtml(q.question)}</h6>
                        ${q.sample_answer ? `
                            <div class="answer-preview small mb-1">💡 Sample answer available</div>
                            <a href="javascript:void(0)" class="answer-link" onclick="toggleAnswer('${uniqueId}')">
                                <i class="bx bx-chevron-down" id="${uniqueId}-icon"></i>
                                <span id="${uniqueId}-text">Show answer</span>
                            </a>
                            <div class="answer-box" id="${uniqueId}">
                                <strong class="d-block mb-1" style="color:#16a34a;">
                                    <i class="bx bx-bulb me-1"></i>Sample Answer
                                </strong>
                                <p class="mb-0 small" style="line-height:1.5;">${escapeHtml(q.sample_answer)}</p>
                            </div>
                        ` : ''}
                        ${q.tips && q.tips.length > 0 ? `
                            <a href="javascript:void(0)" class="tips-link" onclick="toggleTips('${uniqueId}-tips')">
                                <i class="bx bx-bulb"></i>
                                <span>Tips</span>
                            </a>
                            <div class="tips-box" id="${uniqueId}-tips" style="display: none;">
                                <ul class="small mb-0">
                                    ${q.tips.map(tip => `<li>${escapeHtml(tip)}</li>`).join('')}
                                </ul>
                            </div>
                        ` : ''}
                    </div>
                `;
                
                questionCount++;

                // Add secondary CTA every 3 questions
                if (questionCount % 3 === 0 && absoluteIndex + 1 < visibleQuestions.length) {
                    html += `
                        <div class="secondary-cta-wrapper">
                            <a href="{{ route('user.pricing') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-crown me-1"></i> Get AI Feedback on Your Answers
                            </a>
                        </div>
                    `;
                }
            });

            // Add load more button if there are more questions
            if (endIndex < allQuestions.length) {
                html += `
                    <div class="text-center mt-4 mb-4">
                        <button class="btn btn-outline-primary btn-sm" onclick="loadMoreQuestions()" id="loadMoreBtn">
                            <i class="bx bx-plus me-1"></i> Load more questions (${allQuestions.length - endIndex} remaining)
                        </button>
                    </div>
                `;
            }

            questionsList.innerHTML = html;

            // Update progress bar
            const totalVisible = visibleQuestions.length;
            const totalQuestions = allQuestions.length;
            const progressPercent = (totalVisible / totalQuestions) * 100;
            if (progressBar) progressBar.style.width = progressPercent + '%';
        }'''

content = content.replace(old_renderquestions, new_renderquestions)

# 2. Update CSS for cards - reduce padding
old_css_cards = '''        .question-card {
            border-left: 3px solid #667eea;
            margin-bottom: 0.8rem;
            padding: 0.9rem;
            background: #f8f9fa;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .question-card:hover {
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }

        .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            margin-right: 0.5rem;
            flex-shrink: 0;
        }'''

new_css_cards = '''        .question-section-header {
            font-size: 0.85rem;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.75rem 0 0.5rem 0;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .question-card {
            border-left: 3px solid #667eea;
            margin-bottom: 0.5rem;
            padding: 0.55rem 0.75rem;
            background: #f8f9fa;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .question-card:hover {
            box-shadow: 0 2px 6px rgba(102, 126, 234, 0.1);
            background: #ffffff;
        }

        .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            margin-right: 0.5rem;
            flex-shrink: 0;
        }

        .question-text {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1f2937;
            line-height: 1.4;
            margin-bottom: 0.5rem;
        }

        .answer-preview {
            color: #6b7280;
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
        }

        .answer-link, .tips-link {
            color: #667eea !important;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.85rem;
            margin-top: 0.35rem;
            transition: color 0.2s ease;
        }

        .answer-link:hover, .tips-link:hover {
            color: #5568d3 !important;
        }

        .secondary-cta-wrapper {
            text-align: center;
            margin: 1.5rem 0 1rem 0;
            padding: 0.75rem 0;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .badge-success {
            background-color: #10b981 !important;
        }

        .badge-warning {
            background-color: #f59e0b !important;
        }

        .badge-danger {
            background-color: #ef4444 !important;
        }'''

content = content.replace(old_css_cards, new_css_cards)

# 3. Update tips box CSS
old_tips_css = '''        .tips-box {
            background: #f3f4f6;
            border-radius: 4px;
            padding: 0.6rem 0.7rem;
            margin-top: 0.5rem;
            font-size: 0.8125rem;
            color: #6b7280;
            border-left: 2px solid #d1d5db;
        }

        .tips-box ul {
            margin-bottom: 0;
            padding-left: 1.2rem;
        }

        .tips-box li {
            margin-bottom: 0.25rem;
        }'''

new_tips_css = '''        .tips-box {
            background: #f3f4f6;
            border-radius: 4px;
            padding: 0.5rem 0.6rem;
            margin-top: 0.35rem;
            font-size: 0.8rem;
            color: #6b7280;
            border-left: 2px solid #d1d5db;
            display: none;
        }

        .tips-box.show {
            display: block;
            animation: slideDown 0.2s ease;
        }

        .tips-box ul {
            margin-bottom: 0;
            padding-left: 1.1rem;
        }

        .tips-box li {
            margin-bottom: 0.15rem;
            line-height: 1.4;
        }'''

content = content.replace(old_tips_css, new_tips_css)

# 4. Update sidebar to have better styling
old_sidebar = '''                        <!-- Right Sidebar -->
                        <div class="col-lg-4 d-none d-lg-block" style="position: sticky; top: 100px; align-self: flex-start;">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-2"><i class="bx bx-rocket me-1" style="color: #667eea;"></i> Improve Faster</h6>
                                    <p class="small text-muted mb-3">Get AI feedback on your answers and personalized tips.</p>
                                    <a href="{{ route('user.pricing') }}" class="btn btn-primary btn-sm w-100">
                                        <i class="bx bx-crown me-1"></i> Unlock AI Feedback
                                    </a>
                                </div>
                            </div>
                        </div>'''

new_sidebar = '''                        <!-- Right Sidebar -->
                        <div class="col-lg-4 d-none d-lg-block" style="position: sticky; top: 100px; align-self: flex-start;">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f0f4ff 0%, #f5f7ff 100%); border: 1px solid #e0e7ff;">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-2" style="color: #1f2937;"><i class="bx bx-rocket me-1" style="color: #667eea; font-size: 1.1rem;"></i> Improve Faster</h6>
                                    <p class="small text-muted mb-3" style="line-height: 1.5;">Get AI feedback on your answers and personalized tips to boost confidence.</p>
                                    <a href="{{ route('user.pricing') }}" class="btn btn-primary btn-sm w-100" style="background-color: #667eea; border-color: #667eea;">
                                        <i class="bx bx-crown me-1"></i> Unlock AI Feedback
                                    </a>
                                </div>
                            </div>
                        </div>'''

content = content.replace(old_sidebar, new_sidebar)

# Write back the file
with open('resources/views/user/interview/prep.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("✓ All improvements applied successfully!")
