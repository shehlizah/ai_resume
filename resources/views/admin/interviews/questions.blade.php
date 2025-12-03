<x-layouts.app :title="__('Interview Question Bank')">
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Interview Question Bank</h4>
            <p class="text-muted mb-0">Browse all AI-generated interview questions and answers</p>
        </div>
        <div>
            <a href="{{ route('admin.interviews.sessions') }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back me-1'></i>Back to Sessions
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-primary bg-opacity-10 rounded me-3">
                            <i class="bx bx-message-dots text-primary" style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Questions</small>
                            <h4 class="mb-0">{{ number_format($stats['total_questions']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success bg-opacity-10 rounded me-3">
                            <i class="bx bx-check text-success" style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Answered</small>
                            <h4 class="mb-0">{{ number_format($stats['answered_questions']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-info bg-opacity-10 rounded me-3">
                            <i class="bx bx-bar-chart text-info" style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Avg Score</small>
                            <h4 class="mb-0">{{ number_format($stats['avg_score'], 1) }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block mb-2">By Type</small>
                    @foreach($stats['by_type'] as $type => $count)
                        <span class="badge bg-label-primary me-1">{{ ucfirst($type) }}: {{ $count }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.interviews.questions') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Search Question</label>
                    <input type="text" class="form-control" name="search"
                        placeholder="Search question text..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Question Type</label>
                    <select class="form-select" name="type">
                        <option value="">All Types</option>
                        <option value="technical" {{ request('type') === 'technical' ? 'selected' : '' }}>Technical</option>
                        <option value="behavioral" {{ request('type') === 'behavioral' ? 'selected' : '' }}>Behavioral</option>
                        <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>General</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class='bx bx-search me-1'></i>Filter
                    </button>
                    <a href="{{ route('admin.interviews.questions') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Questions Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 45%">Question & Answer</th>
                            <th style="width: 10%">Type</th>
                            <th style="width: 15%">User/Job</th>
                            <th style="width: 10%">Score</th>
                            <th style="width: 15%">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                        <tr>
                            <td>{{ $question->question_number }}</td>
                            <td>
                                <div class="mb-2">
                                    <strong class="d-block mb-1 text-primary">Q: {{ $question->question_text }}</strong>
                                    @if($question->focus_area)
                                        <small class="text-muted d-block">
                                            <i class='bx bx-target-lock'></i> Focus: {{ $question->focus_area }}
                                        </small>
                                    @endif
                                </div>

                                @if($question->answer_text)
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small class="text-muted d-block mb-1"><strong>Answer:</strong></small>
                                        <small class="d-block">{{ Str::limit($question->answer_text, 200) }}</small>

                                        @if(strlen($question->answer_text) > 200)
                                            <button class="btn btn-sm btn-link p-0 mt-1" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#answer-{{ $question->id }}">
                                                View full answer
                                            </button>
                                            <div class="collapse mt-2" id="answer-{{ $question->id }}">
                                                <small class="d-block">{{ $question->answer_text }}</small>

                                                @if($question->feedback && is_array($question->feedback))
                                                    <div class="mt-3">
                                                        @if(isset($question->feedback['feedback']))
                                                            <div class="mb-2">
                                                                <small class="text-primary"><strong>Feedback:</strong></small>
                                                                <small class="d-block">{{ $question->feedback['feedback'] }}</small>
                                                            </div>
                                                        @endif

                                                        @if(isset($question->feedback['strengths']) && count($question->feedback['strengths']) > 0)
                                                            <div class="mb-2">
                                                                <small class="text-success"><strong>Strengths:</strong></small>
                                                                <ul class="mb-0">
                                                                    @foreach($question->feedback['strengths'] as $strength)
                                                                        <li><small>{{ $strength }}</small></li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif

                                                        @if(isset($question->feedback['improvements']) && count($question->feedback['improvements']) > 0)
                                                            <div class="mb-2">
                                                                <small class="text-warning"><strong>Areas to Improve:</strong></small>
                                                                <ul class="mb-0">
                                                                    @foreach($question->feedback['improvements'] as $improvement)
                                                                        <li><small>{{ $improvement }}</small></li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <small class="text-muted"><em>Not answered yet</em></small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $question->question_type === 'technical' ? 'primary' : ($question->question_type === 'behavioral' ? 'info' : 'secondary') }}">
                                    {{ ucfirst($question->question_type) }}
                                </span>
                            </td>
                            <td>
                                @if($question->session && $question->session->user)
                                    <small class="d-block">{{ $question->session->user->name }}</small>
                                    <small class="text-muted d-block">{{ $question->session->job_title }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($question->score)
                                    <span class="badge {{ $question->score >= 70 ? 'bg-success' : ($question->score >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($question->score, 0) }}%
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $question->created_at->format('M d, Y') }}</small>
                                <small class="text-muted d-block">{{ $question->created_at->format('h:i A') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class='bx bx-message-square-x' style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mt-2">No questions found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $questions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
