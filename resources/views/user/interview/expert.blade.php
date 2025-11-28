@section('title', __('Book Expert Interview Session'))
<x-layouts.app :title="__('Book Expert Session')">
    <div class="row g-4">
        <!-- Header -->
        <div class="col-lg-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <h4 class="text-white mb-2">
                        <i class="bx bx-user-check me-2"></i> Book Expert Interview Session
                    </h4>
                    <p class="text-white mb-0 opacity-90">
                        Get personalized coaching from industry experts
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <div id="expertsContainer">
                @foreach($experts as $expert)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="avatar avatar-lg bg-primary bg-opacity-10 rounded-circle mx-auto mb-2"
                                     style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bx bxs-user text-primary" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <h6 class="mb-1">{{ $expert['name'] }}</h6>
                                <p class="text-success small mb-2">
                                    <strong>{{ $expert['title'] }}</strong>
                                </p>
                                <p class="text-muted small mb-2">
                                    <i class="bx bx-briefcase me-1"></i> {{ $expert['experience'] }}
                                </p>
                                <p class="text-muted small mb-0">{{ $expert['bio'] }}</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="mb-3">
                                    <h5 class="text-primary mb-2">{{ $expert['rate'] }}</h5>
                                </div>
                                <button class="btn btn-primary btn-sm w-100 mb-2" data-bs-toggle="modal"
                                        data-bs-target="#bookModal{{ $expert['id'] }}">
                                    <i class="bx bx-calendar me-1"></i> Book Session
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Book Modal -->
                <div class="modal fade" id="bookModal{{ $expert['id'] }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content border-0">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">Book with {{ $expert['name'] }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label mb-3">Available Time Slots</label>
                                <div id="slotsContainer{{ $expert['id'] }}">
                                    @foreach($expert['available_slots'] as $slot)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="slot{{ $expert['id'] }}"
                                               value="{{ $slot }}" id="slot{{ $expert['id'] }}{{ $loop->index }}">
                                        <label class="form-check-label" for="slot{{ $expert['id'] }}{{ $loop->index }}">
                                            <i class="bx bx-calendar-alt me-1"></i> {{ $slot }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary"
                                        onclick="bookSession({{ $expert['id'] }})">Confirm Booking</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- What You'll Get -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-check-circle me-1"></i> What You'll Get
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="ps-3 small">
                        <li class="mb-2">1-on-1 coaching session</li>
                        <li class="mb-2">Personalized feedback</li>
                        <li class="mb-2">Resume review tips</li>
                        <li class="mb-2">Interview strategy</li>
                        <li class="mb-2">Career guidance</li>
                        <li>Follow-up email summary</li>
                    </ul>
                </div>
            </div>

            <!-- How It Works -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-info-circle me-1"></i> How It Works
                    </h6>
                </div>
                <div class="card-body small">
                    <div class="mb-3">
                        <div class="badge bg-primary mb-2">1</div>
                        <p>Select an expert and available time slot</p>
                    </div>
                    <div class="mb-3">
                        <div class="badge bg-primary mb-2">2</div>
                        <p>Confirm your booking</p>
                    </div>
                    <div class="mb-0">
                        <div class="badge bg-primary mb-2">3</div>
                        <p>Join the video call at the scheduled time</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function bookSession(expertId) {
        const selectedSlot = document.querySelector(`input[name="slot${expertId}"]:checked`);

        if (!selectedSlot) {
            alert('Please select a time slot');
            return;
        }

        fetch('{{ route("user.interview.book-session") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                expert_id: expertId,
                time_slot: selectedSlot.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Session booked successfully!');
                window.location.href = data.next_page;
            } else {
                alert(data.message || 'Error booking session');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error booking session');
        });
    }
    </script>
</x-layouts.app>
