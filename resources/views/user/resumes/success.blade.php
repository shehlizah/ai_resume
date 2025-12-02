<x-layouts.app :title="'Resume Created Successfully'">
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Success Message -->
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Main Success Card -->
                <div class="card border-success mb-4">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bx bx-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="text-success mb-3">🎉 Resume Created Successfully!</h2>
                        <p class="text-muted mb-4">
                            Your professional resume "<strong>{{ $resume->title }}</strong>" has been generated and is ready to download.
                        </p>

                        <div class="d-flex justify-content-center gap-3 mb-4">
                            <a href="{{ route('user.resumes.view', $resume->id) }}"
                               class="btn btn-primary btn-lg" target="_blank">
                                <i class="bx bx-show me-1"></i> View Resume
                            </a>
                            <a href="{{ route('user.resumes.download', $resume->id) }}"
                               class="btn btn-success btn-lg">
                                <i class="bx bx-download me-1"></i> Download PDF
                            </a>
                        </div>

                        <a href="{{ route('user.resumes.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-folder me-1"></i> View All My Resumes
                        </a>
                    </div>
                </div>



                <!-- What's Next Section -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📋 What's Next?</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-edit text-primary me-3" style="font-size: 2rem;"></i>
                                    <div>
                                        <h6>Customize Your Resume</h6>
                                        <small class="text-muted">
                                            Edit and tailor your resume for specific job applications
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-envelope text-success me-3" style="font-size: 2rem;"></i>
                                    <div>
                                        <h6>Create a Cover Letter</h6>
                                        <small class="text-muted">
                                            Complement your resume with a professional cover letter
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-briefcase text-info me-3" style="font-size: 2rem;"></i>
                                    <div>
                                        <h6>Find Job Opportunities</h6>
                                        <small class="text-muted">
                                            Use our Job Finder to discover verified job boards
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-user-voice text-warning me-3" style="font-size: 2rem;"></i>
                                    <div>
                                        <h6>Prepare for Interviews</h6>
                                        <small class="text-muted">
                                            Get our Interview Prep kit to ace your next interview
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Auto-open PDF in new tab -->
    <script>
        // Auto-open the resume PDF in a new tab
        window.addEventListener('load', function() {
            const viewUrl = "{{ route('user.resumes.view', $resume->id) }}";
            // Uncomment the line below if you want to auto-open the PDF
            // window.open(viewUrl, '_blank');
        });
    </script>
</x-layouts.app>
