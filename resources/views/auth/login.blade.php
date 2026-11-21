<div class="col-md-12">
    <div id="login" class="modal fade" aria-modal="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content brand-card p-4">
                <div class="modal-body p-2">
                    <form action="{{ route('login') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center mb-4">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                                <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Court of Arms of the United Republic of Tanzania" height="72" style="filter:drop-shadow(0 6px 16px rgba(255, 191, 0,0.28));">
                            </div>
                            <h4 style="font-weight:700;color:#ffffff;margin-bottom:4px;">Sign in to GeMEMIS</h4>
                            <p style="font-size:13px;color:rgba(255,255,255,0.65);margin:0;">Geita Mineral Exhibition Management Information System</p>
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label text-warning small fw-bold">Username / Email / Phone *</label>
                            <input type="text" name="user_name" class="form-control brand-field" placeholder="Enter username" value="{{ old('user_name') }}" required>
                            @error('user_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label text-warning small fw-bold">Password *</label>
                            <input type="password" name="password" class="form-control brand-field" placeholder="Enter password" required>
                            @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 small">
                            <label class="d-flex align-items-center gap-2 m-0 text-white-50">
                                <input type="checkbox" name="remember" class="brand-checkbox"> Remember
                            </label>
                            <a href="{{ route('password.request') }}" class="text-warning text-decoration-none fw-semibold">Forgot password?</a>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn-brand py-2.5">
                                Login to Portal <i class="fa fa-arrow-right ms-1"></i>
                            </button>
                        </div>

                        <div class="text-center text-white-50 small mb-3">
                            Don't have an account? <a href="{{ route('registration') }}" class="text-warning text-decoration-none fw-semibold">Register here</a>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary text-white-50 border-secondary rounded-pill" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
